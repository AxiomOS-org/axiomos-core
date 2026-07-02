<?php

declare(strict_types=1);

namespace App\ADT\Marketplace;

use RuntimeException;

final class PackageManifestValidator
{
    private const SEMVER = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/';

    public function validate(PackageManifest $manifest, string $coreVersion): void
    {
        if (preg_match(self::SEMVER, $manifest->version) !== 1) {
            throw new RuntimeException("Invalid package version: {$manifest->version}");
        }

        if (preg_match(self::SEMVER, $manifest->minimumCoreVersion) !== 1) {
            throw new RuntimeException("Invalid minimumCoreVersion: {$manifest->minimumCoreVersion}");
        }

        if (version_compare($coreVersion, $manifest->minimumCoreVersion, '<')) {
            throw new RuntimeException(sprintf(
                'Package [%s] requires core >= %s',
                $manifest->name,
                $manifest->minimumCoreVersion,
            ));
        }

        $allowedTypes = ['module', 'plugin', 'theme', 'ai-agent', 'workflow', 'automation', 'marketplace-package'];

        if (! in_array($manifest->type, $allowedTypes, true)) {
            throw new RuntimeException("Unsupported package type: {$manifest->type}");
        }
    }
}
