<?php

declare(strict_types=1);

namespace App\ADT\Marketplace\Lifecycle;

final class InstallResult
{
    public function __construct(
        public readonly string $packageName,
        public readonly string $targetPath,
        public readonly bool $installed,
        public readonly string $message,
    ) {
    }
}
