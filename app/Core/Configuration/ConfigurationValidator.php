<?php

declare(strict_types=1);

namespace App\Core\Configuration;

use App\Core\Configuration\Contracts\ConfigurationValidatorInterface;
use App\Core\Configuration\Exceptions\InvalidConfigurationException;
use App\Core\Configuration\Support\ArrayPath;

/**
 * Default configuration validator for kernel boot.
 */
final class ConfigurationValidator implements ConfigurationValidatorInterface
{
    /**
     * @param list<string>              $required
     * @param array<string, list<mixed>> $allowed
     */
    public function __construct(
        private readonly array $required = ['app.name', 'app.env'],
        private readonly array $allowed = [
            'app.env' => ['local', 'testing', 'staging', 'production'],
        ],
    ) {
    }

    public function validate(array $configuration): void
    {
        $violations = [];

        foreach ($this->required as $key) {
            if (! ArrayPath::has($configuration, $key)) {
                $violations[] = sprintf('Missing required configuration key "%s".', $key);
            }
        }

        foreach ($this->allowed as $key => $allowedValues) {
            $value = ArrayPath::get($configuration, $key);

            if ($value === null) {
                continue;
            }

            if (! in_array($value, $allowedValues, true)) {
                $violations[] = sprintf(
                    'Configuration key "%s" must be one of [%s], "%s" given.',
                    $key,
                    implode(', ', array_map(static fn (mixed $item): string => (string) $item, $allowedValues)),
                    (string) $value,
                );
            }
        }

        if ($violations !== []) {
            throw InvalidConfigurationException::withViolations($violations);
        }
    }
}
