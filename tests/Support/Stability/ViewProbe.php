<?php

declare(strict_types=1);

namespace Tests\Support\Stability;

use App\Infrastructure\View\BladeBootstrap;
use Illuminate\View\ViewException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class ViewProbe
{
    /**
     * @return list<string>
     */
    public static function moduleViewPaths(string $basePath): array
    {
        $paths = [];
        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

        if (! is_dir($modulesPath)) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($modulesPath, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths);

        return $paths;
    }

    /**
     * @param list<string> $viewDirectories
     *
     * @return list<string> compilation failures
     */
    public static function compileAll(string $basePath, array $viewDirectories): array
    {
        BladeBootstrap::reset();

        $cachePath = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views';
        $factory = BladeBootstrap::boot($cachePath, $viewDirectories);
        $failures = [];

        foreach (self::discoverViewNames($basePath) as $viewName) {
            foreach ([self::defaultViewData(), self::strictViewData()] as $dataSet) {
                try {
                    $factory->make($viewName, $dataSet)->render();
                } catch (ViewException $exception) {
                    $failures[] = $viewName . ': ' . $exception->getMessage();
                } catch (\Throwable $exception) {
                    $failures[] = $viewName . ': ' . $exception->getMessage();
                }
            }
        }

        return $failures;
    }

    /**
     * Minimal view data for strict undefined-variable detection.
     * Views must use null-coalescing defaults for optional variables.
     *
     * @return array<string, mixed>
     */
    public static function strictViewData(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public static function discoverViewNames(string $basePath): array
    {
        $views = [];

        foreach (self::moduleViewPaths($basePath) as $path) {
            if (! preg_match('#modules[/\\\\][^/\\\\]+[/\\\\]Resources[/\\\\]views[/\\\\](.+)\.blade\.php$#', $path, $matches)) {
                continue;
            }

            $views[] = str_replace(['/', '\\'], '.', $matches[1]);
        }

        sort($views);

        return array_values(array_unique($views));
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultViewData(): array
    {
        return [
            'title' => 'Stability Probe',
            'active' => 'dashboard',
            'entity' => 'identities',
            'entityLabel' => 'Identities',
            'entityTitle' => 'Identities',
            'entityDescription' => 'Stability probe page.',
            'apiBase' => '/api/identities',
            'columns' => ['id', 'name', 'status'],
            'fields' => ['name', 'status'],
            'config' => [
                'title' => 'Identities',
                'columns' => [
                    ['key' => 'id', 'label' => 'ID'],
                    ['key' => 'name', 'label' => 'Name'],
                ],
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                ],
            ],
            'cards' => [
                ['label' => 'Identities', 'count' => 0, 'path' => '/identity/identities'],
            ],
            'readOnly' => false,
            'rows' => [],
            'token' => 'probe-token',
            'email' => 'probe@axiomos.local',
        ];
    }

    /**
     * @return list<string>
     */
    public static function viewDirectories(string $basePath): array
    {
        $directories = [];
        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views', GLOB_ONLYDIR) ?: [] as $directory) {
            $directories[] = $directory;
        }

        $themeViews = $basePath . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'Default' . DIRECTORY_SEPARATOR . 'views';
        if (is_dir($themeViews)) {
            $directories[] = $themeViews;
        }

        return $directories;
    }
}
