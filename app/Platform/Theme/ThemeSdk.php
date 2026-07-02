<?php

declare(strict_types=1);

namespace App\Platform\Theme;

final class ThemeSdk
{
    public function __construct(
        private readonly ThemeResolver $resolver,
    ) {
    }

    public function activeTheme(): string
    {
        return $this->resolver->active()->name();
    }

    public function renderLayout(string $layout, array $data = []): string
    {
        $layoutPath = $this->resolver->active()->layout($layout);
        extract($data, EXTR_SKIP);
        ob_start();
        include $layoutPath;

        return (string) ob_get_clean();
    }

    /**
     * @return list<string>
     */
    public function availableThemes(): array
    {
        return array_map(static fn ($theme): string => $theme->name(), $this->resolver->all());
    }
}
