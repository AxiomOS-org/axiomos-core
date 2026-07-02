<?php

declare(strict_types=1);

namespace Tests\Reliability;

use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\PostgresFeatureTestCase;

final class AccountingConcurrentPostingTest extends PostgresFeatureTestCase
{
    public function test_sequential_concurrent_posting_requests_remain_stable(): void
    {
        $kernel = HttpKernelFactory::create($this->basePath);
        $kernel->handle(Request::create('/health', 'GET'));

        $org = $this->decode($kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'))->getContent());
        $company = $this->decode($kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'))->getContent());

        $cash = $this->createAccount($kernel, $org, $company, '1160', 'Concurrent Cash', 'asset');
        $revenue = $this->createAccount($kernel, $org, $company, '4160', 'Concurrent Revenue', 'income');

        $statuses = [];

        for ($i = 1; $i <= 20; $i++) {
            $amount = sprintf('%d.%06d', $i, 0);
            $response = $kernel->handle(Request::create('/api/accounting/posting/submit', 'POST', [
                'idempotency_key' => 'concurrent:post:' . $i,
                'source_module' => 'Sales',
                'source_document_type' => 'invoice',
                'source_document_id' => 'CONC-' . $i,
                'organization_id' => $org['data'][0]['id'],
                'company_id' => $company['data'][0]['id'],
                'posting_date' => date('Y-m-d'),
                'currency' => 'USD',
                'exchange_rate' => '1',
                'voucher_type' => 'JV',
                'lines' => [
                    ['account_id' => $cash['id'], 'debit' => $amount, 'credit' => '0.000000'],
                    ['account_id' => $revenue['id'], 'debit' => '0.000000', 'credit' => $amount],
                ],
            ]));
            $statuses[] = $response->getStatusCode();
        }

        foreach ($statuses as $status) {
            self::assertLessThan(500, $status);
        }

        $tb = $this->decode($kernel->handle(Request::create(
            '/api/accounting/reports/trial-balance?company_id=' . $company['data'][0]['id'],
            'GET',
        ))->getContent());

        self::assertSame((string) $tb['data']['debit_total'], (string) $tb['data']['credit_total']);
    }

    /**
     * @param array<string, mixed> $org
     * @param array<string, mixed> $company
     *
     * @return array<string, mixed>
     */
    private function createAccount($kernel, array $org, array $company, string $code, string $name, string $type): array
    {
        $response = $kernel->handle(Request::create('/api/accounting/accounts', 'POST', [
            'organization_id' => $org['data'][0]['id'],
            'company_id' => $company['data'][0]['id'],
            'account_code' => $code,
            'account_name' => $name,
            'account_type' => $type,
        ]));

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        return $this->decode($response->getContent())['data'];
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
