<?php

declare(strict_types=1);

namespace App\ADT\Release;

use App\ADT\Upgrade\UpgradeEngine;

final class ReleaseManager
{
    public function __construct(
        private readonly string $basePath,
        private readonly string $coreVersion,
        private readonly UpgradeEngine $upgradeEngine = new UpgradeEngine(),
        private readonly ChangelogGenerator $changelogGenerator = new ChangelogGenerator(),
    ) {
    }

    public function readiness(): ReleaseReadinessReport
    {
        $checks = [];
        $failures = [];
        $modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules';

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'module.json') ?: [] as $manifestPath) {
            $moduleName = basename(dirname($manifestPath));
            $contents = file_get_contents($manifestPath);

            if ($contents === false) {
                $failures[] = "Unreadable manifest: {$moduleName}";
                continue;
            }

            $data = json_decode($contents, true);

            if (! is_array($data)) {
                $failures[] = "Invalid manifest JSON: {$moduleName}";
                continue;
            }

            $checks[] = "Manifest valid: {$moduleName}";

            if (($data['enabled'] ?? false) === true) {
                $moduleDir = dirname($manifestPath);

                if (is_file($moduleDir . DIRECTORY_SEPARATOR . 'TESTING.md')) {
                    $drift = $this->upgradeEngine->detectDrift($moduleDir, $moduleName);

                    if ($drift->hasDrift) {
                        $failures[] = "Blueprint drift detected: {$moduleName}";
                    } else {
                        $checks[] = "No blueprint drift: {$moduleName}";
                    }
                }
            }
        }

        $requiredPaths = [
            'artisan',
            'app/ADT',
            'app/Platform',
            'docs/architecture/ARCHITECTURE_FREEZE.md',
        ];

        foreach ($requiredPaths as $relative) {
            $path = $this->basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

            if (! file_exists($path)) {
                $failures[] = "Missing required path: {$relative}";
            } else {
                $checks[] = "Required path present: {$relative}";
            }
        }

        return new ReleaseReadinessReport(
            version: $this->coreVersion,
            ready: $failures === [],
            checks: $checks,
            failures: $failures,
        );
    }

    public function generateChangelog(): string
    {
        $modulesPath = $this->basePath . DIRECTORY_SEPARATOR . 'modules';
        $entries = $this->changelogGenerator->fromModuleChangelogs($modulesPath);

        return $this->changelogGenerator->compile($this->coreVersion, $entries);
    }
}
