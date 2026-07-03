<?php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CorsMiddleware
{
    /** @var list<string> */
    private const ALLOWED_ORIGINS = [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ];

    /** @var list<string> */
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /** @var list<string> */
    private const ALLOWED_HEADERS = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-Session-Id',
        'X-Company-Id',
    ];

    public function handle(Request $request, callable $next): Response
    {
        $origin = (string) $request->headers->get('Origin', '');

        if ($request->getMethod() === 'OPTIONS') {
            return $this->applyHeaders(new Response('', Response::HTTP_NO_CONTENT), $origin);
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->applyHeaders($response, $origin);
    }

    private function applyHeaders(Response $response, string $origin): Response
    {
        if ($origin !== '' && in_array($origin, self::ALLOWED_ORIGINS, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        $response->headers->set('Access-Control-Allow-Methods', implode(', ', self::ALLOWED_METHODS));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', self::ALLOWED_HEADERS));

        return $response;
    }
}
