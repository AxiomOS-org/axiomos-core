<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
final class BalanceSheetService { public function __construct(private readonly TrialBalanceService $tb) {} public function generate(string $companyId): array { $tb=$this->tb->generate($companyId); return ['assets'=>$tb['debit_total'],'liabilities_and_equity'=>$tb['credit_total']]; } }

