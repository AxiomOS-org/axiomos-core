<?php

declare(strict_types=1);

namespace App\ADT\MakeModule;

use InvalidArgumentException;

/**
 * Validates module names for ADT generation.
 */
final class ModuleNameValidator
{
    public function validate(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Module name is required.');
        }

        if (! preg_match('/^[A-Z][A-Za-z0-9]*$/', $name)) {
            throw new InvalidArgumentException(
                'Module name must be PascalCase and contain only letters and numbers.',
            );
        }

        return $name;
    }
}
