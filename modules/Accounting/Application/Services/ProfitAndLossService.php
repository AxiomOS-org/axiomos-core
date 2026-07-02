<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
final class ProfitAndLossService { public function __construct(private readonly TrialBalanceService $tb) {} public function generate(string $companyId): array { $tb=$this->tb->generate($companyId); return ['income'=>$tb['credit_total'],'expense'=>$tb['debit_total'],'net_profit'=>MoneyMath::sub((string)$tb['credit_total'],(string)$tb['debit_total'])]; } }

