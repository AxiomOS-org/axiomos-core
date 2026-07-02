<?php

declare(strict_types=1);

namespace App\Platform\Integration\Contracts;

interface ConnectorInterface
{
    public function name(): string;

    public function connect(array $config): void;

    public function send(string $endpoint, array $payload): array;
}
