<?php

declare(strict_types=1);

namespace App\Core\Module\Exceptions;

use RuntimeException;

/**
 * Raised when a module folder exposes a manifest that cannot be trusted.
 *
 * Discovery distinguishes between an *invalid folder* (a directory without a
 * `module.json`, which is silently ignored) and an *invalid module* (a folder
 * that ships a manifest but the manifest is unreadable, malformed, or is
 * missing a required field). Only the latter raises this exception.
 *
 * Every factory produces a self-describing message that identifies the exact
 * manifest and the exact reason it was rejected, so callers never have to guess
 * which module broke the boot sequence.
 */
final class InvalidModuleException extends RuntimeException
{
    /**
     * The configured modules directory does not exist or is not a directory.
     */
    public static function missingModulesPath(string $modulesPath): self
    {
        return new self(sprintf(
            'Modules path "%s" does not exist or is not a directory.',
            $modulesPath,
        ));
    }

    /**
     * The manifest exists but could not be read from disk.
     */
    public static function unreadableManifest(string $manifestPath): self
    {
        return new self(sprintf(
            'Module manifest "%s" could not be read.',
            $manifestPath,
        ));
    }

    /**
     * The manifest is not valid JSON, or does not decode to an object.
     */
    public static function malformedJson(string $manifestPath, string $reason): self
    {
        return new self(sprintf(
            'Module manifest "%s" contains invalid JSON: %s',
            $manifestPath,
            $reason,
        ));
    }

    /**
     * A required manifest field is absent.
     */
    public static function missingField(string $manifestPath, string $field): self
    {
        return new self(sprintf(
            'Module manifest "%s" is missing the required field "%s".',
            $manifestPath,
            $field,
        ));
    }

    /**
     * A required manifest field is present but has the wrong type.
     */
    public static function invalidFieldType(string $manifestPath, string $field, string $expectedType): self
    {
        return new self(sprintf(
            'Module manifest "%s" field "%s" must be of type %s.',
            $manifestPath,
            $field,
            $expectedType,
        ));
    }

    /**
     * A required string field is present but empty.
     */
    public static function emptyField(string $manifestPath, string $field): self
    {
        return new self(sprintf(
            'Module manifest "%s" field "%s" must not be empty.',
            $manifestPath,
            $field,
        ));
    }

    /**
     * The version (or minimum core version) is not valid semantic versioning.
     */
    public static function invalidVersion(string $manifestPath, string $version): self
    {
        return new self(sprintf(
            'Module manifest "%s" declares version "%s", which is not valid semantic versioning.',
            $manifestPath,
            $version,
        ));
    }

    /**
     * The declared UUID is not a valid UUID string.
     */
    public static function invalidUuid(string $manifestPath, string $uuid): self
    {
        return new self(sprintf(
            'Module manifest "%s" declares an invalid UUID "%s".',
            $manifestPath,
            $uuid,
        ));
    }

    /**
     * The declared provider class cannot be resolved by the autoloader.
     */
    public static function providerNotFound(string $manifestPath, string $providerClass): self
    {
        return new self(sprintf(
            'Module manifest "%s" declares provider "%s", which does not exist.',
            $manifestPath,
            $providerClass,
        ));
    }

    /**
     * The running kernel is older than the module's minimum supported version.
     */
    public static function unsupportedCoreVersion(string $manifestPath, string $required, string $current): self
    {
        return new self(sprintf(
            'Module manifest "%s" requires core version "%s" but the running core is "%s".',
            $manifestPath,
            $required,
            $current,
        ));
    }

    /**
     * Two modules declare the same name.
     */
    public static function duplicateName(string $name, string $firstPath, string $secondPath): self
    {
        return new self(sprintf(
            'Duplicate module name "%s" declared by both "%s" and "%s".',
            $name,
            $firstPath,
            $secondPath,
        ));
    }

    /**
     * A module depends on another module that was not discovered.
     */
    public static function missingDependency(string $module, string $dependency): self
    {
        return new self(sprintf(
            'Module "%s" depends on "%s", which is not installed.',
            $module,
            $dependency,
        ));
    }
}
