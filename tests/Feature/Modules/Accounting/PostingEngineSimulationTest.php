<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Accounting;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Modules\Accounting\Application\Services\MoneyMath;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\PostgresFeatureTestCase;

/**
 * Stress-tests the posting engine with randomized balanced journals.
 */
final class PostingEngineSimulationTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    /** @var array<string, mixed> */
    private array $organization;

    /** @var array<string, mixed> */
    private array $company;

    /** @var array<string, mixed> */
    private array $cashAccount;

    /** @var array<string, mixed> */
    private array $revenueAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
        $this->waitForOrganizationsApi();
        $this->bootstrapAccounts();
    }

    private function waitForOrganizationsApi(): void
    {
        $lastContent = '';
        $deadline = time() + 90;

        while (time() < $deadline) {
            $this->kernel->handle(Request::create('/health', 'GET'));
            $response = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
            $lastContent = (string) $response->getContent();

            if ($response->getStatusCode() === Response::HTTP_OK) {
                return;
            }

            usleep(250_000);
        }

        self::fail('Organization API not ready: ' . $lastContent);
    }

    public function test_posting_engine_handles_1000_balanced_simulations(): void
    {
        $imbalances = [];
        $failures = [];

        for ($i = 1; $i <= 1000; $i++) {
            $amount = $this->randomAmount();
            $response = $this->submitPosting($i, $amount);
            $status = $response->getStatusCode();

            if ($status !== Response::HTTP_OK) {
                $failures[] = sprintf('Simulation %d returned HTTP %d', $i, $status);
                continue;
            }

            $payload = $this->decode($response->getContent());
            if (! ($payload['success'] ?? false)) {
                $failures[] = sprintf('Simulation %d failed: %s', $i, json_encode($payload['errors'] ?? []));
            }
        }

        $tb = $this->trialBalance();
        if ($tb['debit_total'] !== $tb['credit_total']) {
            $imbalances[] = sprintf(
                'Trial balance imbalance after 1000 postings: debit=%s credit=%s',
                $tb['debit_total'],
                $tb['credit_total'],
            );
        }

        self::assertSame([], $failures, implode("\n", array_slice($failures, 0, 20)));
        self::assertSame([], $imbalances, implode("\n", $imbalances));
    }

    public function test_posting_preview_rejects_unbalanced_entries(): void
    {
        $response = $this->kernel->handle(Request::create('/api/accounting/posting/preview', 'POST', [
            'idempotency_key' => 'preview:unbalanced:1',
            'source_module' => 'Sales',
            'source_document_type' => 'invoice',
            'source_document_id' => 'INV-UNBAL-1',
            'organization_id' => $this->organization['id'],
            'company_id' => $this->company['id'],
            'posting_date' => date('Y-m-d'),
            'currency' => 'USD',
            'exchange_rate' => '1',
            'voucher_type' => 'JV',
            'lines' => [
                ['account_id' => $this->cashAccount['id'], 'debit' => '100.000000', 'credit' => '0.000000'],
                ['account_id' => $this->revenueAccount['id'], 'debit' => '0.000000', 'credit' => '50.000000'],
            ],
        ]));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $payload = $this->decode($response->getContent());
        self::assertFalse($payload['data']['valid'] ?? true);
        self::assertNotEmpty($payload['data']['errors'] ?? []);
    }

    public function test_idempotent_posting_does_not_double_ledger(): void
    {
        $amount = '250.000000';
        $key = 'idempotent:probe:1';

        $first = $this->submitPostingWithKey($key, $amount);
        $second = $this->submitPostingWithKey($key, $amount);

        self::assertSame(Response::HTTP_OK, $first->getStatusCode());
        self::assertSame(Response::HTTP_OK, $second->getStatusCode());

        $firstPayload = $this->decode($first->getContent());
        $secondPayload = $this->decode($second->getContent());

        self::assertTrue($firstPayload['success']);
        self::assertTrue($secondPayload['success']);
        self::assertSame($firstPayload['journal_id'], $secondPayload['journal_id']);

        $tb = $this->trialBalance();
        self::assertSame('250.000000', (string) $tb['debit_total']);
        self::assertSame('250.000000', (string) $tb['credit_total']);
    }

    public function test_sequential_postings_maintain_ledger_balance(): void
    {
        $runningDebit = '0.000000';

        for ($i = 1; $i <= 50; $i++) {
            $amount = $this->randomAmount();
            $response = $this->submitPosting(2000 + $i, $amount);
            self::assertSame(Response::HTTP_OK, $response->getStatusCode());

            $runningDebit = MoneyMath::add($runningDebit, $amount);
            $tb = $this->trialBalance();

            self::assertSame($runningDebit, (string) $tb['debit_total'], "Debit drift at iteration {$i}");
            self::assertSame($runningDebit, (string) $tb['credit_total'], "Credit drift at iteration {$i}");
        }
    }

    private function bootstrapAccounts(): void
    {
        $organizationResponse = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $organizationResponse->getStatusCode(), (string) $organizationResponse->getContent());
        $this->organization = $this->decode($organizationResponse->getContent())['data'][0];

        $companyResponse = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $companyResponse->getStatusCode(), (string) $companyResponse->getContent());
        $this->company = $this->decode($companyResponse->getContent())['data'][0];

        $accountsResponse = $this->kernel->handle(Request::create(
            '/api/accounting/accounts?company_id=' . $this->company['id'],
            'GET',
        ));
        self::assertSame(Response::HTTP_OK, $accountsResponse->getStatusCode());
        $accounts = $this->decode($accountsResponse->getContent())['data'] ?? [];

        if (count($accounts) >= 2) {
            $this->cashAccount = $accounts[0];
            $this->revenueAccount = $accounts[1];

            return;
        }

        $this->cashAccount = $this->createAccount('1150', 'Simulation Cash', 'asset');
        $this->revenueAccount = $this->createAccount('4150', 'Simulation Revenue', 'income');
    }

    /**
     * @return array<string, mixed>
     */
    private function createAccount(string $code, string $name, string $type): array
    {
        $response = $this->kernel->handle(Request::create('/api/accounting/accounts', 'POST', [
            'organization_id' => $this->organization['id'],
            'company_id' => $this->company['id'],
            'account_code' => $code,
            'account_name' => $name,
            'account_type' => $type,
        ]));

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());

        return $this->decode($response->getContent())['data'];
    }

    private function randomAmount(): string
    {
        return sprintf('%d.%06d', random_int(1, 9999), random_int(0, 999999));
    }

    private function submitPosting(int $sequence, string $amount): Response
    {
        return $this->submitPostingWithKey('sim:post:' . $sequence, $amount);
    }

    private function submitPostingWithKey(string $key, string $amount): Response
    {
        return $this->kernel->handle(Request::create('/api/accounting/posting/submit', 'POST', [
            'idempotency_key' => $key,
            'source_module' => 'Sales',
            'source_document_type' => 'invoice',
            'source_document_id' => 'SIM-' . $key,
            'organization_id' => $this->organization['id'],
            'company_id' => $this->company['id'],
            'posting_date' => date('Y-m-d'),
            'currency' => 'USD',
            'exchange_rate' => '1',
            'voucher_type' => 'JV',
            'lines' => [
                ['account_id' => $this->cashAccount['id'], 'debit' => $amount, 'credit' => '0.000000'],
                ['account_id' => $this->revenueAccount['id'], 'debit' => '0.000000', 'credit' => $amount],
            ],
        ]));
    }

    /**
     * @return array{debit_total: string, credit_total: string}
     */
    private function trialBalance(): array
    {
        $response = $this->kernel->handle(Request::create(
            '/api/accounting/reports/trial-balance?company_id=' . $this->company['id'],
            'GET',
        ));

        $payload = $this->decode($response->getContent());

        return [
            'debit_total' => (string) ($payload['data']['debit_total'] ?? '0'),
            'credit_total' => (string) ($payload['data']['credit_total'] ?? '0'),
        ];
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
