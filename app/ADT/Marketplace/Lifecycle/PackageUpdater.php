<?php

declare(strict_types=1);

namespace App\ADT\Marketplace\Lifecycle;

use App\ADT\Marketplace\PackageManifest;
use RuntimeException;

final class PackageUpdater
{
    public function update(PackageManifest $manifest, string $installedPath, PackageInstaller $installer): InstallResult
    {
        if (! is_dir($installedPath)) {
            throw new RuntimeException("Installed package not found: {$installedPath}");
        }

        $backupPath = $installedPath . '.bak.' . time();
        rename($installedPath, $backupPath);

        try {
            $parent = dirname($installedPath);

            return $installer->install($manifest, $parent);
        } catch (\Throwable $exception) {
            if (is_dir($backupPath)) {
                rename($backupPath, $installedPath);
            }

            throw $exception;
        } finally {
            if (is_dir($backupPath)) {
                $this->removeDirectory($backupPath);
            }
        }
    }

    private function removeDirectory(string $directory): void
    {
        $items = scandir($directory);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
