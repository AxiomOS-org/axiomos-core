<?php

declare(strict_types=1);

namespace App\ADT\Marketplace;

use App\ADT\Marketplace\Lifecycle\InstallResult;
use App\ADT\Marketplace\Lifecycle\PackageInstaller;
use App\ADT\Marketplace\Lifecycle\PackageRollback;
use App\ADT\Marketplace\Lifecycle\PackageUpdater;

final class MarketplaceSdk
{
    public function __construct(
        private readonly string $packagesPath,
        private readonly string $installRoot,
        private readonly string $coreVersion,
        private readonly PackageManifestValidator $validator = new PackageManifestValidator(),
        private readonly IntegrityVerifier $integrityVerifier = new IntegrityVerifier(),
    ) {
    }

    /**
     * @return list<PackageManifest>
     */
    public function catalog(): array
    {
        if (! is_dir($this->packagesPath)) {
            return [];
        }

        $packages = [];

        foreach (glob($this->packagesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
            $packages[] = PackageManifest::fromDirectory($directory);
        }

        return $packages;
    }

    public function install(string $packageName): InstallResult
    {
        $manifest = $this->resolvePackage($packageName);
        $installer = new PackageInstaller($this->coreVersion, $this->validator, $this->integrityVerifier);

        return $installer->install($manifest, $this->installRoot);
    }

    public function update(string $packageName): InstallResult
    {
        $manifest = $this->resolvePackage($packageName);
        $installedPath = $this->installRoot . DIRECTORY_SEPARATOR . $packageName;
        $installer = new PackageInstaller($this->coreVersion, $this->validator, $this->integrityVerifier);
        $updater = new PackageUpdater();

        return $updater->update($manifest, $installedPath, $installer);
    }

    public function rollback(string $packageName, string $backupPath): InstallResult
    {
        $installedPath = $this->installRoot . DIRECTORY_SEPARATOR . $packageName;

        return (new PackageRollback())->rollback($installedPath, $backupPath);
    }

    private function resolvePackage(string $packageName): PackageManifest
    {
        foreach ($this->catalog() as $manifest) {
            if ($manifest->name === $packageName) {
                return $manifest;
            }
        }

        throw new \RuntimeException("Package not found in catalog: {$packageName}");
    }
}
