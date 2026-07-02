<?php

declare(strict_types=1);

namespace App\Platform\Integration;

final class IntegrationSdk
{
    public function __construct(
        private readonly IntegrationRegistry $registry,
    ) {
    }

    public function registerConnector(Contracts\ConnectorInterface $connector): void
    {
        $this->registry->registerConnector($connector);
    }

    public function registerWebhook(Contracts\WebhookHandlerInterface $handler): void
    {
        $this->registry->registerWebhook($handler);
    }

    public function send(string $connectorName, string $endpoint, array $payload): array
    {
        return $this->registry->connector($connectorName)->send($endpoint, $payload);
    }

    public function receive(string $event, array $payload): void
    {
        $this->registry->handleWebhook($event, $payload);
    }
}
