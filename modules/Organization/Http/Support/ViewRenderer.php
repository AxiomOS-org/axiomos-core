<?php

declare(strict_types=1);

namespace Modules\Organization\Http\Support;

use Symfony\Component\HttpFoundation\Response;

final class ViewRenderer
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = []): Response
    {
        $path = dirname(__DIR__, 2) . '/Resources/views/' . $view . '.php';

        if (! is_file($path)) {
            return new Response('View not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $path;

        return new Response(
            (string) ob_get_clean(),
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }
}
