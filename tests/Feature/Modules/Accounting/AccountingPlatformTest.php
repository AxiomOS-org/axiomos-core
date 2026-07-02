<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Accounting;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\PostgresFeatureTestCase;

final class AccountingPlatformTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
        $this->kernel->handle(Request::create('/health', 'GET'));
    }

    public function test_full_posting_flow(): void
    {
        $organizationResponse = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $organizationResponse->getStatusCode(), (string) $organizationResponse->getContent());
        $organization = $this->decode($organizationResponse->getContent())['data'][0];

        $companyResponse = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $companyResponse->getStatusCode(), (string) $companyResponse->getContent());
        $company = $this->decode($companyResponse->getContent())['data'][0];

        $cashResponse = $this->kernel->handle(Request::create('/api/accounting/accounts', 'POST', [
            'organization_id' => $organization['id'],
            'company_id' => $company['id'],
            'account_code' => '1100',
            'account_name' => 'Bank',
            'account_type' => 'asset',
        ]));
        self::assertSame(Response::HTTP_CREATED, $cashResponse->getStatusCode(), (string) $cashResponse->getContent());
        $cash = $this->decode($cashResponse->getContent())['data'];

        $revenueResponse = $this->kernel->handle(Request::create('/api/accounting/accounts', 'POST', [
            'organization_id' => $organization['id'],
            'company_id' => $company['id'],
            'account_code' => '4100',
            'account_name' => 'Service Revenue',
            'account_type' => 'income',
        ]));
        self::assertSame(Response::HTTP_CREATED, $revenueResponse->getStatusCode(), (string) $revenueResponse->getContent());
        $revenue = $this->decode($revenueResponse->getContent())['data'];

        $response = $this->kernel->handle(Request::create('/api/accounting/posting/submit', 'POST', [
            'idempotency_key' => 't:acc:submit:1',
            'source_module' => 'Sales',
            'source_document_type' => 'invoice',
            'source_document_id' => 'INV-ACC-001',
            'organization_id' => $organization['id'],
            'company_id' => $company['id'],
            'posting_date' => date('Y-m-d'),
            'currency' => 'USD',
            'exchange_rate' => '1',
            'voucher_type' => 'JV',
            'lines' => [
                ['account_id' => $cash['id'], 'debit' => '500.000000', 'credit' => '0.000000'],
                ['account_id' => $revenue['id'], 'debit' => '0.000000', 'credit' => '500.000000'],
            ],
        ]));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
        $posting = $this->decode($response->getContent());
        self::assertTrue($posting['success']);

        $tb = $this->decode($this->kernel->handle(Request::create(
            '/api/accounting/reports/trial-balance?company_id=' . $company['id'],
            'GET',
        ))->getContent());

        self::assertSame('500.000000', (string) $tb['data']['debit_total']);
        self::assertSame('500.000000', (string) $tb['data']['credit_total']);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string|false $content): array
    {
        self::assertIsString($content);

        /** @var array<string, mixed> */
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
