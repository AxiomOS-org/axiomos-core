<?php

declare(strict_types=1);

namespace Tests\Unit\ADT;

use App\ADT\Marketplace\IntegrityVerifier;
use App\ADT\Marketplace\MarketplaceSdk;
use App\ADT\Marketplace\PackageManifest;
use PHPUnit\Framework\TestCase;

final class MarketplaceSdkTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'axiomos_pkg_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function test_it_installs_catalog_package(): void
    {
        $packageDir = $this->tempDir . DIRECTORY_SEPARATOR . 'Catalog' . DIRECTORY_SEPARATOR . 'DemoPkg';
        mkdir($packageDir, 0777, true);
        file_put_contents($packageDir . DIRECTORY_SEPARATOR . 'package.json', json_encode([
            'name' => 'DemoPkg',
            'type' => 'module',
            'version' => '1.0.0',
            'minimumCoreVersion' => '1.0.0',
            'dependencies' => [],
            'checksum' => '',
        ], JSON_THROW_ON_ERROR));
        file_put_contents($packageDir . DIRECTORY_SEPARATOR . 'README.md', 'demo');

        $installRoot = $this->tempDir . DIRECTORY_SEPARATOR . 'installed';
        mkdir($installRoot, 0777, true);

        $sdk = new MarketplaceSdk($this->tempDir . DIRECTORY_SEPARATOR . 'Catalog', $installRoot, '1.0.0');
        $result = $sdk->install('DemoPkg');

        self::assertTrue($result->installed);
        self::assertDirectoryExists($installRoot . DIRECTORY_SEPARATOR . 'DemoPkg');
    }

    public function test_integrity_verifier_detects_tampering(): void
    {
        $packageDir = $this->tempDir . DIRECTORY_SEPARATOR . 'Pkg';
        mkdir($packageDir, 0777, true);
        file_put_contents($packageDir . DIRECTORY_SEPARATOR . 'file.txt', 'original');

        $verifier = new IntegrityVerifier();
        $checksum = $verifier->computeDirectoryChecksum($packageDir);

        $manifest = new PackageManifest('Pkg', 'module', '1.0.0', '1.0.0', [], $checksum, $packageDir);
        $verifier->verify($manifest);

        file_put_contents($packageDir . DIRECTORY_SEPARATOR . 'file.txt', 'tampered');

        $this->expectException(\RuntimeException::class);
        $verifier->verify($manifest);
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $item) {
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
