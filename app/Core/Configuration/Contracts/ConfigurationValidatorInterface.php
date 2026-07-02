<?php

declare(strict_types=1);

namespace App\Core\Configuration\Contracts;

/**
 * Validates the fully merged configuration tree.
 */
interface ConfigurationValidatorInterface
{
    /**
     * @param array<string, mixed> $configuration
   *
   * @throws \App\Core\Configuration\Exceptions\InvalidConfigurationException
   */
    public function validate(array $configuration): void;
}
