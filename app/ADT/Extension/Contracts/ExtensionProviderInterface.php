<?php

declare(strict_types=1);

namespace App\ADT\Extension\Contracts;

/**
 * Extension point provider loaded from installed plugins.
 */
interface ExtensionProviderInterface
{
    public function name(): string;

    public function version(): string;

    /**
     * @return list<string>
     */
    public function capabilities(): array;

    public function register(ExtensionRegistryInterface $registry): void;
}
