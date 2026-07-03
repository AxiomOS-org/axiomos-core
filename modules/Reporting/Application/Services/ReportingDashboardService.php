<?php
declare(strict_types=1);
namespace Modules\Reporting\Application\Services;
use Modules\Accounting\Application\Services\TrialBalanceService;
use Modules\Accounting\Application\Services\ProfitAndLossService;
use Modules\Accounting\Application\Services\BalanceSheetService;
final class ReportingDashboardService {
    public function __construct(private readonly TrialBalanceService $trialBalance, private readonly ProfitAndLossService $pnl, private readonly BalanceSheetService $balanceSheet) {}
    public function dashboard(string $companyId): array {
        if ($companyId === '') { return []; }
        return ['trial_balance' => $this->trialBalance->generate($companyId), 'profit_loss' => $this->pnl->generate($companyId), 'balance_sheet' => $this->balanceSheet->generate($companyId)];
    }
}