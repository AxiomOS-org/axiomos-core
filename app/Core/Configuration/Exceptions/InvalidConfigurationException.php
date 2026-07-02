<?php

declare(strict_types=1);

namespace App\Core\Configuration\Exceptions;

final class InvalidConfigurationException extends ConfigurationException
{
    /**
     * @param list<string> $violations
     */
    public function __construct(string $message, public readonly array $violations)
    {
        parent::__construct($message);
    }

    /**
     * @param list<string> $violations
     */
    public static function withViolations(array $violations): self
    {
        return new self(sprintf(
            'Configuration validation failed: %s',
            implode('; ', $violations),
        ), $violations);
    }
}
