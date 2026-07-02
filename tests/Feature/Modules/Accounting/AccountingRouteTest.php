<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Accounting;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\PostgresFeatureTestCase;

/**
 * Verifies every Accounting API and web route avoids HTTP 500.
 */
final class AccountingRouteTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    /** @var array<string, mixed> */
    private array $company = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
        $this->kernel->handle(Request::create('/health', 'GET'));

        $response = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));
        $payload = $this->decode($response->getContent());
        $this->company = $payload['data'][0] ?? [];
    }

    public function test_accounting_get_routes_do_not_return_500(): void
    {
        $companyId = (string) ($this->company['id'] ?? '');
        $failures = [];

        $routes = [
            '/accounting',
            '/accounting/dashboard',
            '/accounting/accounts',
            '/accounting/documents',
            '/accounting/journals',
            '/accounting/fiscal-years',
            '/accounting/periods',
            '/api/accounting/accounts?company_id=' . $companyId,
            '/api/accounting/fiscal-years?company_id=' . $companyId,
            '/api/accounting/periods?company_id=' . $companyId,
            '/api/accounting/voucher-types?company_id=' . $companyId,
            '/api/accounting/documents?company_id=' . $companyId,
            '/api/accounting/journals',
            '/api/accounting/dimensions/cost-centers?company_id=' . $companyId,
            '/api/accounting/dimensions/profit-centers?company_id=' . $companyId,
            '/api/accounting/reports/trial-balance?company_id=' . $companyId,
            '/api/accounting/reports/balance-sheet?company_id=' . $companyId,
            '/api/accounting/reports/profit-loss?company_id=' . $companyId,
            '/api/accounting/reports/cash-flow?company_id=' . $companyId,
            '/api/accounting/exchange-rates?company_id=' . $companyId,
        ];

        foreach ($routes as $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET', server: [
                'HTTP_ACCEPT' => str_starts_with($path, '/api/') ? 'application/json' : 'text/html',
            ]));

            if ($response->getStatusCode() >= 500) {
                $failures[] = sprintf('GET %s returned %d', $path, $response->getStatusCode());
            }
        }

        self::assertSame([], $failures, implode("\n", $failures));
    }

    public function test_accounting_post_routes_do_not_return_500(): void
    {
        $failures = [];
        $orgResponse = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
        $org = $this->decode($orgResponse->getContent())['data'][0] ?? [];
        $companyId = (string) ($this->company['id'] ?? '');

        $postRoutes = [
            ['/api/accounting/accounts', [
                'organization_id' => $org['id'] ?? '',
                'company_id' => $companyId,
                'account_code' => '9999',
                'account_name' => 'Route Probe',
                'account_type' => 'expense',
            ]],
            ['/api/accounting/posting/preview', [
                'idempotency_key' => 'route:preview:1',
                'source_module' => 'Sales',
                'source_document_type' => 'invoice',
                'source_document_id' => 'ROUTE-1',
                'organization_id' => $org['id'] ?? '',
                'company_id' => $companyId,
                'posting_date' => date('Y-m-d'),
                'currency' => 'USD',
                'exchange_rate' => '1',
                'voucher_type' => 'JV',
                'lines' => [],
            ]],
            ['/api/accounting/posting/submit', [
                'idempotency_key' => 'route:submit:empty',
                'source_module' => 'Sales',
                'source_document_type' => 'invoice',
                'source_document_id' => 'ROUTE-2',
                'organization_id' => $org['id'] ?? '',
                'company_id' => $companyId,
                'posting_date' => date('Y-m-d'),
                'currency' => 'USD',
                'exchange_rate' => '1',
                'voucher_type' => 'JV',
                'lines' => [],
            ]],
            ['/api/accounting/posting/reverse', [
                'idempotency_key' => 'route:reverse:1',
                'journal_id' => '00000000-0000-4000-8000-000000000099',
                'company_id' => $companyId,
                'organization_id' => $org['id'] ?? '',
                'reason' => 'Route probe',
            ]],
        ];

        foreach ($postRoutes as [$path, $payload]) {
            $response = $this->kernel->handle(Request::create($path, 'POST', $payload, server: [
                'HTTP_ACCEPT' => 'application/json',
            ]));

            if ($response->getStatusCode() >= 500) {
                $failures[] = sprintf('POST %s returned %d: %s', $path, $response->getStatusCode(), (string) $response->getContent());
            }
        }

        self::assertSame([], $failures, implode("\n", $failures));
    }

    public function test_accounting_web_pages_render_without_undefined_variables(): void
    {
        $needles = ['Undefined variable', 'Undefined array key', 'ViewException'];
        $leaks = [];

        foreach ([
            '/accounting',
            '/accounting/dashboard',
            '/accounting/accounts',
            '/accounting/documents',
            '/accounting/journals',
            '/accounting/fiscal-years',
            '/accounting/periods',
        ] as $path) {
            $content = (string) $this->kernel->handle(Request::create($path, 'GET'))->getContent();

            foreach ($needles as $needle) {
                if (str_contains($content, $needle)) {
                    $leaks[] = sprintf('%s leaked "%s"', $path, $needle);
                }
            }
        }

        self::assertSame([], $leaks, implode("\n", $leaks));
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string|false $content): array
    {
        if (! is_string($content)) {
            return [];
        }

        /** @var array<string, mixed> */
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
