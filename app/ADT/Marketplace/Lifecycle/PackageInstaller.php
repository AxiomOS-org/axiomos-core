<?php

declare(strict_types=1);

namespace App\ADT\Marketplace\Lifecycle;

use App\ADT\Marketplace\IntegrityVerifier;
use App\ADT\Marketplace\PackageManifest;
use App\ADT\Marketplace\PackageManifestValidator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class PackageInstaller
{
    public function __construct(
        private readonly string $coreVersion,
        private readonly PackageManifestValidator $validator = new PackageManifestValidator(),
        private readonly IntegrityVerifier $integrityVerifier = new IntegrityVerifier(),
    ) {
    }

    public function install(PackageManifest $manifest, string $targetRoot): InstallResult
    {
        $this->validator->validate($manifest, $this->coreVersion);
        $this->integrityVerifier->verify($manifest);

        $targetPath = $targetRoot . DIRECTORY_SEPARATOR . $manifest->name;

        if (is_dir($targetPath)) {
            throw new RuntimeException("Target already exists: {$targetPath}");
        }

        $this->copyDirectory($manifest->path, $targetPath);

        return new InstallResult(
            packageName: $manifest->name,
            targetPath: $targetPath,
            installed: true,
            message: 'Package installed successfully.',
        );
    }

    private function copyDirectory(string $source, string $destination): void
    {
        if (! mkdir($destination, 0777, true) && ! is_dir($destination)) {
            throw new RuntimeException("Failed to create directory: {$destination}");
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (! is_dir($target) && ! mkdir($target, 0777, true) && ! is_dir($target)) {
                    throw new RuntimeException("Failed to create directory: {$target}");
                }
            } else {
                $parent = dirname($target);

                if (! is_dir($parent) && ! mkdir($parent, 0777, true) && ! is_dir($parent)) {
                    throw new RuntimeException("Failed to create directory: {$parent}");
                }

                if (! copy($item->getPathname(), $target)) {
                    throw new RuntimeException("Failed to copy file to: {$target}");
                }
            }
        }
    }
}
