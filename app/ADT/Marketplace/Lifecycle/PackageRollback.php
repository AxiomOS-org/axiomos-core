<?php

declare(strict_types=1);

namespace App\ADT\Marketplace\Lifecycle;

use RuntimeException;

final class PackageRollback
{
    public function rollback(string $installedPath, string $backupPath): InstallResult
    {
        if (! is_dir($backupPath)) {
            throw new RuntimeException("Backup not found: {$backupPath}");
        }

        if (is_dir($installedPath)) {
            $this->removeDirectory($installedPath);
        }

        rename($backupPath, $installedPath);

        return new InstallResult(
            packageName: basename($installedPath),
            targetPath: $installedPath,
            installed: true,
            message: 'Package rolled back successfully.',
        );
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
