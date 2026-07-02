<?php

declare(strict_types=1);

namespace App\ADT\Upgrade;

final class UpgradePlan
{
    /**
     * @param list<string> $actions
     */
    public function __construct(
        public readonly string $moduleName,
        public readonly string $fromVersion,
        public readonly string $toVersion,
        public readonly array $actions,
        public readonly bool $safe,
    ) {
    }
}
