<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Event;

use App\Core\Event\Contracts\DelayedEvent;
use App\Core\Event\Contracts\EventListenerInterface;
use App\Core\Event\Contracts\EventSubscriberInterface;
use App\Core\Event\Contracts\QueueableEvent;
use App\Core\Event\EventBus;
use App\Core\Event\EventBusBuilder;
use App\Core\Event\Events\AfterDispatch;
use App\Core\Event\Events\BeforeDispatch;
use App\Core\Event\Events\DispatchFailed;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;
use RuntimeException;

final class EventBusTest extends TestCase
{
    private EventBus $bus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = (new EventBusBuilder())->build();
    }

    public function test_it_dispatches_to_a_listener(): void
    {
        $handled = null;
        $this->bus->listen(SampleEvent::class, static function (SampleEvent $event) use (&$handled): void {
            $handled = $event->value;
        });

        $this->bus->dispatch(new SampleEvent('hello'));

        self::assertSame('hello', $handled);
    }

    public function test_it_invokes_listeners_in_priority_order(): void
    {
        $order = [];
        $this->bus->listen(SampleEvent::class, static function () use (&$order): void {
            $order[] = 'low';
        }, priority: 1);
        $this->bus->listen(SampleEvent::class, static function () use (&$order): void {
            $order[] = 'high';
        }, priority: 100);
        $this->bus->listen(SampleEvent::class, static function () use (&$order): void {
            $order[] = 'medium';
        }, priority: 50);

        $this->bus->dispatch(new SampleEvent('x'));

        self::assertSame(['high', 'medium', 'low'], $order);
    }

    public function test_it_supports_wildcard_listeners(): void
    {
        $count = 0;
        $this->bus->listen('*', static function () use (&$count): void {
            $count++;
        });

        $this->bus->dispatch(new SampleEvent('a'));
        $this->bus->dispatch(new OtherEvent());

        self::assertSame(2, $count);
    }

    public function test_it_supports_namespace_wildcard_listeners(): void
    {
        $count = 0;
        $this->bus->listen('Tests\\Feature\\Core\\Event\\*', static function () use (&$count): void {
            $count++;
        });

        $this->bus->dispatch(new SampleEvent('a'));

        self::assertSame(1, $count);
    }

    public function test_once_listeners_only_fire_a_single_time(): void
    {
        $count = 0;
        $this->bus->listen(SampleEvent::class, static function () use (&$count): void {
            $count++;
        }, once: true);

        $this->bus->dispatch(new SampleEvent('a'));
        $this->bus->dispatch(new SampleEvent('b'));

        self::assertSame(1, $count);
    }

    public function test_it_stops_propagation_for_stoppable_events(): void
    {
        $order = [];
        $this->bus->listen(StoppableSampleEvent::class, static function (StoppableSampleEvent $event) use (&$order): void {
            $order[] = 'first';
            $event->stop();
        }, priority: 100);
        $this->bus->listen(StoppableSampleEvent::class, static function () use (&$order): void {
            $order[] = 'second';
        }, priority: 1);

        $this->bus->dispatch(new StoppableSampleEvent());

        self::assertSame(['first'], $order);
    }

    public function test_it_registers_subscribers(): void
    {
        $subscriber = new SampleSubscriber();
        $this->bus->subscribe($subscriber);

        $this->bus->dispatch(new SampleEvent('a'));
        $this->bus->dispatch(new OtherEvent());

        self::assertSame(['sample', 'other-high', 'other-low'], $subscriber->log);
    }

    public function test_it_dispatches_class_based_listeners(): void
    {
        SpyListener::$handled = 0;
        $this->bus->listenClass(SampleEvent::class, SpyListener::class);

        $this->bus->dispatch(new SampleEvent('a'));

        self::assertSame(1, SpyListener::$handled);
    }

    public function test_queueable_events_are_not_handled_until_processed(): void
    {
        $handled = 0;
        $this->bus->listen(QueuedSampleEvent::class, static function () use (&$handled): void {
            $handled++;
        });

        $this->bus->dispatch(new QueuedSampleEvent());
        self::assertSame(0, $handled);

        $processed = $this->bus->processQueue();

        self::assertSame(1, $processed);
        self::assertSame(1, $handled);
    }

    public function test_async_dispatch_enqueues_any_event(): void
    {
        $handled = 0;
        $this->bus->listen(SampleEvent::class, static function () use (&$handled): void {
            $handled++;
        });

        $this->bus->dispatchAsync(new SampleEvent('a'));
        self::assertSame(0, $handled);

        $this->bus->processQueue();
        self::assertSame(1, $handled);
    }

    public function test_delayed_events_are_not_due_until_the_delay_elapses(): void
    {
        $handled = 0;
        $this->bus->listen(SampleEvent::class, static function () use (&$handled): void {
            $handled++;
        });

        $now = microtime(true);
        $this->bus->dispatchDelayed(new SampleEvent('a'), 60);

        self::assertSame(0, $this->bus->processQueue($now));
        self::assertSame(1, $this->bus->processQueue($now + 61));
        self::assertSame(1, $handled);
    }

    public function test_failed_queued_events_are_retried_up_to_max_attempts(): void
    {
        $attempts = 0;
        $this->bus->listen(SampleEvent::class, static function () use (&$attempts): void {
            $attempts++;
            throw new RuntimeException('boom');
        });

        $this->bus->dispatchAsync(new SampleEvent('a'), maxAttempts: 3);

        $now = microtime(true);
        $this->bus->processQueue($now);
        $this->bus->processQueue($now);
        $this->bus->processQueue($now);

        self::assertSame(3, $attempts);
        self::assertSame(2, $this->bus->metrics()->retried());
    }

    public function test_it_fires_meta_events(): void
    {
        $captured = [];
        foreach ([BeforeDispatch::class, AfterDispatch::class] as $meta) {
            $this->bus->listen($meta, static function (object $event) use (&$captured): void {
                $captured[] = $event::class;
            });
        }

        $this->bus->listen(SampleEvent::class, static fn () => null);
        $this->bus->dispatch(new SampleEvent('a'));

        self::assertContains(BeforeDispatch::class, $captured);
        self::assertContains(AfterDispatch::class, $captured);
    }

    public function test_it_fires_dispatch_failed_and_rethrows(): void
    {
        $failed = 0;
        $this->bus->listen(DispatchFailed::class, static function () use (&$failed): void {
            $failed++;
        });
        $this->bus->listen(SampleEvent::class, static function (): void {
            throw new RuntimeException('boom');
        });

        try {
            $this->bus->dispatch(new SampleEvent('a'));
            self::fail('Expected exception to propagate.');
        } catch (RuntimeException $exception) {
            self::assertSame('boom', $exception->getMessage());
        }

        self::assertSame(1, $failed);
        self::assertSame(1, $this->bus->metrics()->failed());
    }

    public function test_it_collects_metrics(): void
    {
        $this->bus->listen(SampleEvent::class, static fn () => null);

        $this->bus->dispatch(new SampleEvent('a'));
        $this->bus->dispatch(new SampleEvent('b'));

        $metrics = $this->bus->metrics()->toArray();

        self::assertSame(2, $metrics['dispatched']);
        self::assertSame(2, $metrics['listener_invocations']);
        self::assertSame(2, $metrics['per_event'][SampleEvent::class]);
    }

    public function test_it_caches_and_reloads_class_listeners(): void
    {
        SpyListener::$handled = 0;
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'axiomos_events_' . uniqid('', true) . '.php';

        $this->bus->listenClass(SampleEvent::class, SpyListener::class, priority: 5);
        $this->bus->cache($path);

        $fresh = (new EventBusBuilder())->build();
        self::assertTrue($fresh->loadCache($path));

        $fresh->dispatch(new SampleEvent('a'));

        self::assertSame(1, SpyListener::$handled);

        unlink($path);
    }
}

final class SampleEvent
{
    public function __construct(public string $value)
    {
    }
}

final class OtherEvent
{
}

final class QueuedSampleEvent implements QueueableEvent
{
    public function maxAttempts(): int
    {
        return 1;
    }
}

final class DelayedSampleEvent implements DelayedEvent
{
    public function maxAttempts(): int
    {
        return 1;
    }

    public function delaySeconds(): int
    {
        return 60;
    }
}

final class StoppableSampleEvent implements StoppableEventInterface
{
    private bool $stopped = false;

    public function stop(): void
    {
        $this->stopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}

final class SpyListener implements EventListenerInterface
{
    public static int $handled = 0;

    public function handle(object $event): void
    {
        self::$handled++;
    }
}

final class SampleSubscriber implements EventSubscriberInterface
{
    /** @var list<string> */
    public array $log = [];

    public function subscribe(): array
    {
        return [
            SampleEvent::class => function (): void {
                $this->log[] = 'sample';
            },
            OtherEvent::class => [
                [function (): void {
                    $this->log[] = 'other-low';
                }, 1],
                [function (): void {
                    $this->log[] = 'other-high';
                }, 100],
            ],
        ];
    }
}
