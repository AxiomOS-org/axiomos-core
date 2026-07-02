<?php

declare(strict_types=1);

namespace App\ADT\Release;

final class ReleaseReadinessReport
{
    /**
     * @param list<string> $checks
     * @param list<string> $failures
     */
    public function __construct(
        public readonly string $version,
        public readonly bool $ready,
        public readonly array $checks,
        public readonly array $failures,
    ) {
    }
}
