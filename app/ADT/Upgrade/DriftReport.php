<?php

declare(strict_types=1);

namespace App\ADT\Upgrade;

final class DriftReport
{
    /**
     * @param list<string> $missingFiles
     * @param list<string> $extraFiles
     */
    public function __construct(
        public readonly string $moduleName,
        public readonly array $missingFiles,
        public readonly array $extraFiles,
        public readonly bool $hasDrift,
    ) {
    }
}
