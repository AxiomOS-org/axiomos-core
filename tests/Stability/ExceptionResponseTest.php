<?php

declare(strict_types=1);

namespace Tests\Stability;

use Illuminate\Http\Request;
use Tests\Support\Stability\KernelTestHarness;

final class ExceptionResponseTest extends KernelTestHarness
{
    public function test_invalid_json_payload_returns_client_error_not_500(): void
    {
        $request = Request::create('/api/users', 'POST', server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $request->initialize(
            [],
            [],
            [],
            [],
            [],
            $request->server->all(),
            '{not-json',
        );

        $response = $this->kernel->handle($request);
        $status = $response->getStatusCode();

        self::assertLessThan(500, $status);
        self::assertGreaterThanOrEqual(400, $status);
    }

    public function test_api_errors_return_json_body(): void
    {
        $response = $this->kernel->handle(Request::create('/api/users/not-a-uuid', 'GET', server: [
            'HTTP_ACCEPT' => 'application/json',
        ]));

        self::assertLessThan(500, $response->getStatusCode());
        self::assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'));
    }
}
