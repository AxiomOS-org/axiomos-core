<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
final class CashFlowService { public function __construct(private readonly ProfitAndLossService $pnl) {} public function generate(string $companyId): array { $p=$this->pnl->generate($companyId); return ['operating'=>$p['net_profit'],'investing'=>'0.000000','financing'=>'0.000000','net_cash_flow'=>$p['net_profit']]; } }

