<?php

declare(strict_types=1);

namespace App\Core\Configuration\Events;

/**
 * Fired when merged configuration fails validation.
 */
final readonly class ConfigurationValidationFailed
{
    /**
     * @param list<string> $violations
     */
    public function __construct(public array $violations)
    {
    }
}
