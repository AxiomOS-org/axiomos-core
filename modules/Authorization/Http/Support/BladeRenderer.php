<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Support;

use App\Infrastructure\View\BladeBootstrap;
use Symfony\Component\HttpFoundation\Response;

final class BladeRenderer
{
    private static bool $booted = false;

    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = [], int $status = Response::HTTP_OK): Response
    {
        self::ensureBooted();

        $content = BladeBootstrap::factory()
            ->make($view, $data)
            ->render();

        return new Response($content, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private static function ensureBooted(): void
    {
        if (self::$booted) {
            return;
        }

        $viewsPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views';

        BladeBootstrap::boot(
            dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
            [$viewsPath]
        );

        self::$booted = true;
    }
}
