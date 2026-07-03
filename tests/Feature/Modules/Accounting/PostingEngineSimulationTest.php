<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Accounting;

use Illuminate\Http\Request;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Application\Services\MoneyMath;
use Modules\Accounting\Application\Services\PostingEngine;
use Modules\Accounting\Application\Services\TrialBalanceService;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Stability\KernelTestHarness;

/**
 * Stress-tests the posting engine with 1000+ randomized balanced journals.
 */
final class PostingEngineSimulationTest extends KernelTestHarness
{
    private PostingEngine $posting;

    private TrialBalanceService $trialBalance;

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
        $this->posting = $this->container()->make(PostingEngine::class);
        $this->trialBalance = $this->container()->make(TrialBalanceService::class);
        $this->bootstrapAccounts();
    }

    public function test_posting_engine_handles_25_balanced_simulations(): void
    {
        $failures = [];

        for ($i = 1; $i <= 25; $i++) {
            $amount = $this->randomAmount();
            $result = $this->submitPosting($i, $amount);

            if (! $result->success) {
                $failures[] = sprintf('Simulation %d failed: %s', $i, implode('; ', $result->errors));
            }
        }

        $tb = $this->trialBalance->generate((string) $this->company['id']);

        self::assertSame([], $failures, implode("\n", $failures));
        self::assertSame((string) $tb['debit_total'], (string) $tb['credit_total']);
    }

    /**
     * @group certification
     */
    public function test_posting_engine_handles_1000_balanced_simulations(): void
    {
        $failures = [];

        for ($i = 1; $i <= 1000; $i++) {
            $amount = $this->randomAmount();
            $result = $this->submitPosting($i, $amount);

            if (! $result->success) {
                $failures[] = sprintf('Simulation %d failed: %s', $i, implode('; ', $result->errors));
            }
        }

        $tb = $this->trialBalance->generate((string) $this->company['id']);

        self::assertSame([], $failures, implode("\n", array_slice($failures, 0, 20)));
        self::assertSame((string) $tb['debit_total'], (string) $tb['credit_total']);
    }

    public function test_posting_preview_rejects_unbalanced_entries(): void
    {
        $preview = $this->posting->preview(new PostingRequest(
            'preview:unbalanced:1',
            'Sales',
            'invoice',
            'INV-UNBAL-1',
            (string) $this->company['id'],
            (string) $this->organization['id'],
            null,
            null,
            date('Y-m-d'),
            'USD',
            '1',
            'JV',
            [
                ['account_id' => $this->cashAccount['id'], 'debit' => '100.000000', 'credit' => '0.000000'],
                ['account_id' => $this->revenueAccount['id'], 'debit' => '0.000000', 'credit' => '50.000000'],
            ],
        ));

        self::assertFalse($preview->balanced);
        self::assertNotEmpty($preview->errors);
    }

    public function test_idempotent_posting_does_not_double_ledger(): void
    {
        $amount = '250.000000';
        $key = 'idempotent:probe:1';

        $tbBefore = $this->trialBalance->generate((string) $this->company['id']);

        $first = $this->submitPostingWithKey($key, $amount);
        $second = $this->submitPostingWithKey($key, $amount);

        self::assertTrue($first->success);
        self::assertTrue($second->success);
        self::assertSame($first->journalId, $second->journalId);

        $tbAfter = $this->trialBalance->generate((string) $this->company['id']);
        $expectedDebit = MoneyMath::add((string) $tbBefore['debit_total'], $amount);

        self::assertSame($expectedDebit, (string) $tbAfter['debit_total']);
        self::assertSame($expectedDebit, (string) $tbAfter['credit_total']);
    }

    public function test_sequential_postings_maintain_ledger_balance(): void
    {
        $runningDebit = '0.000000';

        for ($i = 1; $i <= 50; $i++) {
            $amount = $this->randomAmount();
            $result = $this->submitPosting(2000 + $i, $amount);
            self::assertTrue($result->success, implode('; ', $result->errors));

            $runningDebit = MoneyMath::add($runningDebit, $amount);
            $tb = $this->trialBalance->generate((string) $this->company['id']);

            self::assertSame($runningDebit, (string) $tb['debit_total'], "Debit drift at iteration {$i}");
            self::assertSame($runningDebit, (string) $tb['credit_total'], "Credit drift at iteration {$i}");
        }
    }

    private function bootstrapAccounts(): void
    {
        $organizationResponse = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $organizationResponse->getStatusCode(), (string) $organizationResponse->getContent());
        $this->organization = $this->decodeJson((string) $organizationResponse->getContent())['data'][0];

        $companyResponse = $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'));
        self::assertSame(Response::HTTP_OK, $companyResponse->getStatusCode(), (string) $companyResponse->getContent());
        $this->company = $this->decodeJson((string) $companyResponse->getContent())['data'][0];

        $accountsResponse = $this->kernel->handle(Request::create(
            '/api/accounting/accounts?company_id=' . $this->company['id'],
            'GET',
        ));
        self::assertSame(Response::HTTP_OK, $accountsResponse->getStatusCode());
        $accounts = $this->decodeJson((string) $accountsResponse->getContent())['data'] ?? [];

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

        return $this->decodeJson((string) $response->getContent())['data'];
    }

    private function randomAmount(): string
    {
        return sprintf('%d.%06d', random_int(1, 9999), random_int(0, 999999));
    }

    private function submitPosting(int $sequence, string $amount): \Modules\Accounting\Application\DTOs\PostingResult
    {
        return $this->submitPostingWithKey('sim:post:' . $sequence, $amount);
    }

    private function submitPostingWithKey(string $key, string $amount): \Modules\Accounting\Application\DTOs\PostingResult
    {
        return $this->posting->submit(new PostingRequest(
            $key,
            'Sales',
            'invoice',
            'SIM-' . $key,
            (string) $this->company['id'],
            (string) $this->organization['id'],
            null,
            null,
            date('Y-m-d'),
            'USD',
            '1',
            'JV',
            [
                ['account_id' => $this->cashAccount['id'], 'debit' => $amount, 'credit' => '0.000000'],
                ['account_id' => $this->revenueAccount['id'], 'debit' => '0.000000', 'credit' => $amount],
            ],
        ));
    }
}
