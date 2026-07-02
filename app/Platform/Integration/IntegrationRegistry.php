<?php

declare(strict_types=1);

namespace App\Platform\Integration;

use App\Platform\Integration\Contracts\ConnectorInterface;
use App\Platform\Integration\Contracts\WebhookHandlerInterface;
use RuntimeException;

final class IntegrationRegistry
{
    /** @var array<string, ConnectorInterface> */
    private array $connectors = [];

    /** @var array<string, WebhookHandlerInterface> */
    private array $webhooks = [];

    public function registerConnector(ConnectorInterface $connector): void
    {
        $this->connectors[$connector->name()] = $connector;
    }

    public function registerWebhook(WebhookHandlerInterface $handler): void
    {
        $this->webhooks[$handler->event()] = $handler;
    }

    public function connector(string $name): ConnectorInterface
    {
        if (! isset($this->connectors[$name])) {
            throw new RuntimeException("Connector not registered: {$name}");
        }

        return $this->connectors[$name];
    }

    public function handleWebhook(string $event, array $payload): void
    {
        if (! isset($this->webhooks[$event])) {
            throw new RuntimeException("Webhook handler not registered: {$event}");
        }

        $this->webhooks[$event]->handle($payload);
    }
}
