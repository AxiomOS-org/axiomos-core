<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
use Modules\Accounting\Domain\Repositories\Contracts\LedgerBalanceRepositoryInterface;
final class LedgerEngine { public function __construct(private readonly LedgerBalanceRepositoryInterface $ledger) {} public function applyJournal(array $context,array $lines): void { foreach($lines as $line){$this->ledger->upsertLineBalance(['organization_id'=>$context['organization_id']??null,'company_id'=>$context['company_id'],'branch_id'=>$context['branch_id']??null,'department_id'=>$context['department_id']??null,'period_id'=>$context['period_id']??null,'account_id'=>$line['account_id'],'currency'=>$context['currency']],(string)$line['debit'],(string)$line['credit']);} } }

