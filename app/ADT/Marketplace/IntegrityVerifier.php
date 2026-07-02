<?php

declare(strict_types=1);

namespace App\ADT\Marketplace;

use RuntimeException;

final class IntegrityVerifier
{
    public function computeDirectoryChecksum(string $directory): string
    {
        if (! is_dir($directory)) {
            throw new RuntimeException("Directory not found: {$directory}");
        }

        $files = $this->collectFiles($directory);
        sort($files);

        $hashContext = hash_init('sha256');

        foreach ($files as $file) {
            $relative = substr($file, strlen($directory) + 1);
            hash_update($hashContext, $relative);
            hash_update($hashContext, (string) file_get_contents($file));
        }

        return hash_final($hashContext);
    }

    public function verify(PackageManifest $manifest): void
    {
        if ($manifest->checksum === '') {
            return;
        }

        $actual = $this->computeDirectoryChecksum($manifest->path);

        if (! hash_equals($manifest->checksum, $actual)) {
            throw new RuntimeException("Package integrity check failed for [{$manifest->name}]");
        }
    }

    /**
     * @return list<string>
     */
    private function collectFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $files[] = $fileInfo->getPathname();
            }
        }

        return $files;
    }
}
