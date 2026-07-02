<?php

declare(strict_types=1);

namespace App\Core\Event;

use Psr\Log\LoggerInterface;

/**
 * Fluent builder assembling a fully wired {@see EventBus}.
 */
final class EventBusBuilder
{
    private ?LoggerInterface $logger = null;

    private ?\Closure $listenerResolver = null;

    private int $historySize = 1000;

    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withListenerResolver(callable $resolver): self
    {
        $this->listenerResolver = \Closure::fromCallable($resolver);

        return $this;
    }

    public function withHistorySize(int $size): self
    {
        $this->historySize = max(1, $size);

        return $this;
    }

    public function build(): EventBus
    {
        $provider = new ListenerProvider();

        return new EventBus(
            listeners: $provider,
            dispatcher: new EventDispatcher($provider),
            queue: new InMemoryEventQueue(),
            store: new InMemoryEventStore($this->historySize),
            logger: $this->logger,
            listenerResolver: $this->listenerResolver,
        );
    }
}
