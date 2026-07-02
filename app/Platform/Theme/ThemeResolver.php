<?php

declare(strict_types=1);

namespace App\Platform\Theme;

use App\Platform\Theme\Contracts\ThemeInterface;
use RuntimeException;

final class ThemeResolver
{
    public function __construct(
        private readonly string $themesPath,
        private string $activeTheme = 'Default',
    ) {
    }

    public function setActiveTheme(string $themeName): void
    {
        $this->activeTheme = $themeName;
    }

    public function active(): ThemeInterface
    {
        return $this->resolve($this->activeTheme);
    }

    /**
     * @return list<ThemeInterface>
     */
    public function all(): array
    {
        if (! is_dir($this->themesPath)) {
            return [];
        }

        $themes = [];

        foreach (glob($this->themesPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [] as $directory) {
            $themes[] = ThemeManifest::fromDirectory($directory);
        }

        return $themes;
    }

    public function resolve(string $themeName): ThemeInterface
    {
        foreach ($this->all() as $theme) {
            if ($theme->name() === $themeName) {
                return $theme;
            }
        }

        throw new RuntimeException("Theme not found: {$themeName}");
    }
}
