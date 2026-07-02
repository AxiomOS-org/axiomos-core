<?php

declare(strict_types=1);

namespace App\Core\Event;

use App\Core\Event\Contracts\EventBusInterface;
use App\Core\Event\Contracts\EventSubscriberInterface;
use Closure;

/**
 * Discovers and registers event subscribers with the bus.
 *
 * Accepts explicit subscriber class-strings (typically produced by module
 * scanning) and registers any that implement {@see EventSubscriberInterface},
 * resolving them through an injected factory (usually the service container).
 */
final class EventDiscovery
{
    private readonly Closure $resolver;

    public function __construct(?callable $resolver = null)
    {
        $this->resolver = $resolver !== null
            ? Closure::fromCallable($resolver)
            : static fn (string $class): object => new $class();
    }

    /**
     * @param list<class-string> $subscriberClasses
     */
    public function discover(array $subscriberClasses, EventBusInterface $bus): int
    {
        $registered = 0;

        foreach ($subscriberClasses as $class) {
            if (! is_subclass_of($class, EventSubscriberInterface::class) && ! $this->implementsSubscriber($class)) {
                continue;
            }

            $subscriber = ($this->resolver)($class);

            if ($subscriber instanceof EventSubscriberInterface) {
                $bus->subscribe($subscriber);
                $registered++;
            }
        }

        return $registered;
    }

    private function implementsSubscriber(string $class): bool
    {
        return class_exists($class)
            && in_array(EventSubscriberInterface::class, class_implements($class) ?: [], true);
    }
}
