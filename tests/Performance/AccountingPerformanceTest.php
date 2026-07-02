<?php

declare(strict_types=1);

namespace Tests\Performance;

use Illuminate\Http\Request;
use Tests\Support\QA\PerformanceProbe;

final class AccountingPerformanceTest extends PerformanceProbe
{
    public function test_accounting_trial_balance_responds_within_threshold(): void
    {
        $companyResponse = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));
        $content = $companyResponse->getContent();
        self::assertIsString($content);

        /** @var array<string, mixed> $payload */
        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $companyId = (string) ($payload['data'][0]['id'] ?? '');

        $started = hrtime(true);
        $response = $this->kernel->handle(Request::create(
            '/api/accounting/reports/trial-balance?company_id=' . $companyId,
            'GET',
        ));
        $elapsedMs = (hrtime(true) - $started) / 1_000_000;

        self::assertSame(200, $response->getStatusCode());
        self::assertLessThan(5000.0, $elapsedMs, 'Trial balance exceeded 5s');
    }

    public function test_accounting_posting_preview_is_bounded(): void
    {
        $orgResponse = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
        $companyResponse = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));

        /** @var array<string, mixed> $org */
        $org = json_decode((string) $orgResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        /** @var array<string, mixed> $company */
        $company = json_decode((string) $companyResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $started = hrtime(true);
        $response = $this->kernel->handle(Request::create('/api/accounting/posting/preview', 'POST', [
            'idempotency_key' => 'perf:preview:1',
            'source_module' => 'Sales',
            'source_document_type' => 'invoice',
            'source_document_id' => 'PERF-1',
            'organization_id' => $org['data'][0]['id'] ?? '',
            'company_id' => $company['data'][0]['id'] ?? '',
            'posting_date' => date('Y-m-d'),
            'currency' => 'USD',
            'exchange_rate' => '1',
            'voucher_type' => 'JV',
            'lines' => [],
        ]));
        $elapsedMs = (hrtime(true) - $started) / 1_000_000;

        self::assertLessThan(500, $response->getStatusCode());
        self::assertLessThan(3000.0, $elapsedMs, 'Posting preview exceeded 3s');
    }
}
