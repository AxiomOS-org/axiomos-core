<?php

declare(strict_types=1);

namespace App\Core\Boot;

/**
 * Immutable record of a module that failed during boot.
 */
final readonly class BootFailure
{
    public function __construct(
        public string $moduleName,
        public string $uuid,
        public string $message,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'module' => $this->moduleName,
            'uuid' => $this->uuid,
            'message' => $this->message,
        ];
    }
}
