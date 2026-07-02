<?php

declare(strict_types=1);

namespace App\ADT\Upgrade;

final class UpgradeEngine
{
    public function __construct(
        private readonly DriftDetector $driftDetector = new DriftDetector(),
    ) {
    }

    public function plan(string $modulePath, string $moduleName, string $fromVersion, string $toVersion): UpgradePlan
    {
        $drift = $this->driftDetector->detect($modulePath, $moduleName);
        $actions = [];

        if ($drift->hasDrift) {
            $actions[] = 'Resolve blueprint drift before version upgrade.';
        }

        if (version_compare($toVersion, $fromVersion, '<=')) {
            $actions[] = 'Target version must be greater than current version.';
        } else {
            $actions[] = "Upgrade {$moduleName} from {$fromVersion} to {$toVersion}.";
            $actions[] = 'Run migrations and quality gates.';
        }

        return new UpgradePlan(
            moduleName: $moduleName,
            fromVersion: $fromVersion,
            toVersion: $toVersion,
            actions: $actions,
            safe: ! $drift->hasDrift && version_compare($toVersion, $fromVersion, '>'),
        );
    }

    public function detectDrift(string $modulePath, string $moduleName): DriftReport
    {
        return $this->driftDetector->detect($modulePath, $moduleName);
    }
}
