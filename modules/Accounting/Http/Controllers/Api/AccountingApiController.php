<?php

declare(strict_types=1);

namespace Modules\Accounting\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Application\DTOs\ReversalRequest;
use Modules\Accounting\Application\Services\AuditTrailService;
use Modules\Accounting\Application\Services\BalanceSheetService;
use Modules\Accounting\Application\Services\CashFlowService;
use Modules\Accounting\Application\Services\ChartOfAccountsService;
use Modules\Accounting\Application\Services\CostCenterService;
use Modules\Accounting\Application\Services\DocumentService;
use Modules\Accounting\Application\Services\FiscalYearService;
use Modules\Accounting\Application\Services\JournalListService;
use Modules\Accounting\Application\Services\AccountingPeriodService;
use Modules\Accounting\Application\Services\MultiCurrencyService;
use Modules\Accounting\Application\Services\PostingEngine;
use Modules\Accounting\Application\Services\ProfitAndLossService;
use Modules\Accounting\Application\Services\TrialBalanceService;
use Modules\Accounting\Application\Services\VoucherEngine;
use Symfony\Component\HttpFoundation\Response;

final class AccountingApiController extends ApiController
{
    public function __construct(
        private readonly PostingEngine $posting,
        private readonly ChartOfAccountsService $chartOfAccounts,
        private readonly FiscalYearService $fiscalYears,
        private readonly AccountingPeriodService $periods,
        private readonly VoucherEngine $voucherEngine,
        private readonly DocumentService $documents,
        private readonly CostCenterService $costCenters,
        private readonly MultiCurrencyService $multiCurrency,
        private readonly TrialBalanceService $trialBalance,
        private readonly BalanceSheetService $balanceSheet,
        private readonly ProfitAndLossService $profitAndLoss,
        private readonly CashFlowService $cashFlow,
        private readonly AuditTrailService $auditTrail,
        private readonly JournalListService $journalList,
    ) {
    }

    public function accounts(Request $request): Response
    {
        if ($request->isMethod('post')) {
            return $this->safe(
                fn (): array => ['data' => $this->chartOfAccounts->create($request->all())],
                Response::HTTP_CREATED,
            );
        }

        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->chartOfAccounts->list($companyId)]);
    }

    public function fiscalYears(Request $request): Response
    {
        if ($request->isMethod('post')) {
            return $this->safe(
                fn (): array => ['data' => $this->fiscalYears->create($request->all())],
                Response::HTTP_CREATED,
            );
        }

        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->fiscalYears->list($companyId)]);
    }

    public function periods(Request $request): Response
    {
        if ($request->isMethod('post')) {
            return $this->safe(
                fn (): array => ['data' => $this->periods->create($request->all())],
                Response::HTTP_CREATED,
            );
        }

        if ($request->isMethod('patch')) {
            return $this->safe(fn (): array => [
                'data' => $this->periods->setOpen(
                    (string) $request->input('id'),
                    (bool) $request->input('is_open', false),
                ),
            ]);
        }

        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->periods->list($companyId)]);
    }

    public function voucherTypes(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->voucherEngine->list($companyId)]);
    }

    public function documents(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->documents->list($companyId)]);
    }

    public function journals(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->journalList->list($companyId)]);
    }

    public function postingLogs(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->auditTrail->list($companyId)]);
    }

    public function postingSubmit(Request $request): Response
    {
        try {
            $result = $this->posting->submit(PostingRequest::fromArray($request->all()));

            return $this->ok($result->toArray(), $result->success ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            return $this->ok(['errors' => [$exception->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function postingPreview(Request $request): Response
    {
        return $this->ok(['data' => $this->posting->preview(PostingRequest::fromArray($request->all()))->toArray()]);
    }

    public function postingReverse(Request $request): Response
    {
        try {
            $result = $this->posting->reverse(new ReversalRequest(
                (string) $request->input('journal_id'),
                (string) $request->input('reason', 'Manual reversal'),
                (string) $request->input('idempotency_key'),
                (string) $request->input('company_id'),
                $request->input('organization_id'),
                $request->input('branch_id'),
                $request->input('department_id'),
            ));

            return $this->ok($result->toArray(), $result->success ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            return $this->ok(['errors' => [$exception->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function costCenters(Request $request): Response
    {
        if ($request->isMethod('post')) {
            return $this->safe(
                fn (): array => ['data' => $this->costCenters->create(array_merge($request->all(), ['dimension_type' => 'cost_center']))],
                Response::HTTP_CREATED,
            );
        }

        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->costCenters->costCenters($companyId)]);
    }

    public function profitCenters(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->costCenters->profitCenters($companyId)]);
    }

    public function trialBalance(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? ['rows' => [], 'debit_total' => '0.000000', 'credit_total' => '0.000000'] : $this->trialBalance->generate($companyId)]);
    }

    public function balanceSheet(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? ['assets' => '0.000000', 'liabilities_and_equity' => '0.000000'] : $this->balanceSheet->generate($companyId)]);
    }

    public function profitLoss(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? ['income' => '0.000000', 'expense' => '0.000000', 'net_profit' => '0.000000'] : $this->profitAndLoss->generate($companyId)]);
    }

    public function cashFlow(Request $request): Response
    {
        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? ['operating' => '0.000000', 'investing' => '0.000000', 'financing' => '0.000000', 'net_cash_flow' => '0.000000'] : $this->cashFlow->generate($companyId)]);
    }

    public function exchangeRates(Request $request): Response
    {
        if ($request->isMethod('post')) {
            return $this->safe(
                fn (): array => ['data' => $this->multiCurrency->storeRate($request->all())],
                Response::HTTP_CREATED,
            );
        }

        $companyId = $this->companyId($request);

        return $this->ok(['data' => $companyId === '' ? [] : $this->multiCurrency->listRates($companyId)]);
    }

    private function companyId(Request $request): string
    {
        $fromQuery = trim((string) $request->query('company_id', ''));
        if ($fromQuery !== '') {
            return $fromQuery;
        }

        return trim((string) $request->headers->get('X-Company-Id', ''));
    }
}
