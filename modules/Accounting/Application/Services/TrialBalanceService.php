<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
use Modules\Accounting\Domain\Repositories\Contracts\LedgerBalanceRepositoryInterface;
final class TrialBalanceService { public function __construct(private readonly LedgerBalanceRepositoryInterface $ledger) {} public function generate(string $companyId): array { $rows=$this->ledger->trialBalance($companyId)->all(); $dr='0';$cr='0'; foreach($rows as $row){$dr=MoneyMath::add($dr,(string)($row['closing_debit']??'0'));$cr=MoneyMath::add($cr,(string)($row['closing_credit']??'0'));} return ['rows'=>$rows,'debit_total'=>$dr,'credit_total'=>$cr]; } }

