<?php

declare(strict_types=1);

namespace App\Platform\Theme;

use App\Platform\Theme\Contracts\ThemeInterface;
use JsonException;
use RuntimeException;

final class ThemeManifest implements ThemeInterface
{
    /**
     * @param list<string> $assetPaths
     */
    public function __construct(
        private readonly string $themeName,
        private readonly string $themeVersion,
        private readonly array $assetPaths,
        private readonly string $basePath,
        private readonly string $defaultLayout,
    ) {
    }

    public static function fromDirectory(string $themeDirectory): self
    {
        $manifestPath = $themeDirectory . DIRECTORY_SEPARATOR . 'theme.json';
        $contents = file_get_contents($manifestPath);

        if ($contents === false) {
            throw new RuntimeException("Theme manifest not found: {$manifestPath}");
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid theme manifest JSON', 0, $exception);
        }

        if (! is_array($data) || ! isset($data['name'], $data['version'])) {
            throw new RuntimeException('Theme manifest must include name and version.');
        }

        $assets = [];

        if (isset($data['assets']) && is_array($data['assets'])) {
            $assets = array_values(array_filter($data['assets'], static fn (mixed $v): bool => is_string($v)));
        }

        $defaultLayout = is_string($data['defaultLayout'] ?? null) ? $data['defaultLayout'] : 'app';

        return new self(
            themeName: $data['name'],
            themeVersion: $data['version'],
            assetPaths: $assets,
            basePath: $themeDirectory,
            defaultLayout: $defaultLayout,
        );
    }

    public function name(): string
    {
        return $this->themeName;
    }

    public function version(): string
    {
        return $this->themeVersion;
    }

    public function assetPaths(): array
    {
        return $this->assetPaths;
    }

    public function layout(string $name): string
    {
        $layout = $name === '' ? $this->defaultLayout : $name;
        $path = $this->basePath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';

        if (! is_file($path)) {
            throw new RuntimeException("Theme layout not found: {$layout}");
        }

        return $path;
    }
}
