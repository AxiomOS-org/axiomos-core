<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Http\Request;
use Tests\Support\Stability\KernelTestHarness;
use Tests\Support\Stability\RouteCatalog;

final class BrowserPageTest extends KernelTestHarness
{
    /**
     * @return list<int>
     */
    private function allowedStatuses(): array
    {
        return [200, 302, 401, 403, 404];
    }

    public function test_browser_pages_do_not_return_500(): void
    {
        $failures = [];

        foreach (RouteCatalog::browserGetPages() as $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            $status = $response->getStatusCode();

            if ($status >= 500) {
                $failures[] = sprintf('%s returned %d', $path, $status);
            }
        }

        self::assertSame([], $failures, "Browser pages returning 500:\n" . implode("\n", $failures));
    }

    public function test_browser_pages_return_expected_status_codes(): void
    {
        $unexpected = [];

        foreach (RouteCatalog::browserGetPages() as $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            $status = $response->getStatusCode();

            if (! in_array($status, $this->allowedStatuses(), true)) {
                $unexpected[] = sprintf('%s returned %d', $path, $status);
            }
        }

        self::assertSame([], $unexpected, "Unexpected browser status codes:\n" . implode("\n", $unexpected));
    }

    public function test_rendered_pages_do_not_expose_runtime_errors(): void
    {
        $leaks = [];
        $needles = [
            'Undefined variable',
            'Undefined array key',
            'Trying to access array offset',
            'Fatal error',
            'TypeError',
        ];

        foreach (RouteCatalog::browserGetPages() as $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            $content = (string) $response->getContent();

            foreach ($needles as $needle) {
                if (str_contains($content, $needle)) {
                    $leaks[] = sprintf('%s leaked "%s"', $path, $needle);
                }
            }
        }

        self::assertSame([], $leaks, "Runtime error strings in HTML:\n" . implode("\n", $leaks));
    }
}
