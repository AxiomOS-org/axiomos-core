<?php

declare(strict_types=1);

namespace App\Platform\Services;

use App\Core\Module\ModuleManifest;
use JsonException;

/**
 * Reads AMS v2 frontend metadata from module directories.
 */
final class PluginManifestReader
{
    /** @var array<string, array<string, mixed>> */
    private const FRONTEND_DEFAULTS = [
        'Accounting' => [
            'basePath' => '/accounting',
            'icon' => 'ledger',
            'label' => 'Accounting',
            'entry' => '@axiomos/plugin-accounting',
        ],
        'Sales' => [
            'basePath' => '/sales',
            'icon' => 'cart',
            'label' => 'Sales',
            'entry' => '@axiomos/plugin-sales',
        ],
        'Purchase' => [
            'basePath' => '/purchase',
            'icon' => 'truck',
            'label' => 'Purchase',
            'entry' => '@axiomos/plugin-purchase',
        ],
        'Inventory' => [
            'basePath' => '/inventory',
            'icon' => 'warehouse',
            'label' => 'Inventory',
            'entry' => '@axiomos/plugin-inventory',
        ],
        'HR' => [
            'basePath' => '/hr',
            'icon' => 'users',
            'label' => 'HR & Payroll',
            'entry' => '@axiomos/plugin-hr',
        ],
        'CRM' => [
            'basePath' => '/crm',
            'icon' => 'pipeline',
            'label' => 'CRM',
            'entry' => '@axiomos/plugin-crm',
        ],
        'POS' => [
            'basePath' => '/pos',
            'icon' => 'pos',
            'label' => 'POS',
            'entry' => '@axiomos/plugin-pos',
        ],
        'Manufacturing' => [
            'basePath' => '/manufacturing',
            'icon' => 'factory',
            'label' => 'Manufacturing',
            'entry' => '@axiomos/plugin-manufacturing',
        ],
        'Projects' => [
            'basePath' => '/projects',
            'icon' => 'projects',
            'label' => 'Projects',
            'entry' => '@axiomos/plugin-projects',
        ],
        'FixedAssets' => [
            'basePath' => '/assets',
            'icon' => 'assets',
            'label' => 'Fixed Assets',
            'entry' => '@axiomos/plugin-assets',
        ],
        'Budgeting' => [
            'basePath' => '/budgeting',
            'icon' => 'budget',
            'label' => 'Budgeting',
            'entry' => '@axiomos/plugin-budgeting',
        ],
        'Reporting' => [
            'basePath' => '/reporting',
            'icon' => 'report',
            'label' => 'Reporting',
            'entry' => '@axiomos/plugin-reporting',
        ],
    ];

    /**
     * @return array<string, mixed>
     */
    public function toApiPayload(ModuleManifest $manifest): array
    {
        $raw = $this->readRawManifest($manifest->path);
        $frontend = $this->resolveFrontend($manifest->name, $raw);
        $backend = $this->resolveBackend($manifest, $raw);

        return [
            'uuid' => $manifest->uuid,
            'id' => $manifest->id,
            'name' => $manifest->name,
            'version' => $manifest->version,
            'description' => is_string($raw['description'] ?? null) ? $raw['description'] : '',
            'enabled' => $manifest->enabled,
            'priority' => $manifest->priority,
            'dependencies' => $manifest->dependencies,
            'minimumCoreVersion' => $manifest->minimumCoreVersion,
            'backend' => $backend,
            'frontend' => $frontend,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function readRawManifest(string $modulePath): array
    {
        $manifestPath = $modulePath . DIRECTORY_SEPARATOR . 'module.json';

        if (! is_file($manifestPath)) {
            return [];
        }

        $contents = file_get_contents($manifestPath);

        if ($contents === false) {
            return [];
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>|null
     */
    private function resolveFrontend(string $name, array $raw): ?array
    {
        if (isset($raw['frontend']) && is_array($raw['frontend'])) {
            /** @var array<string, mixed> $frontend */
            $frontend = $raw['frontend'];

            return $frontend;
        }

        if (! isset(self::FRONTEND_DEFAULTS[$name])) {
            return null;
        }

        return self::FRONTEND_DEFAULTS[$name];
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function resolveBackend(ModuleManifest $manifest, array $raw): array
    {
        if (isset($raw['backend']) && is_array($raw['backend'])) {
            /** @var array<string, mixed> $backend */
            $backend = $raw['backend'];

            return $backend;
        }

        return [
            'provider' => $manifest->provider,
            'apiPrefix' => self::apiPrefix($manifest->name),
        ];
    }

    private static function apiPrefix(string $name): string
    {
        return match ($name) {
            'FixedAssets' => '/api/assets',
            default => '/api/' . strtolower($name),
        };
    }
}
