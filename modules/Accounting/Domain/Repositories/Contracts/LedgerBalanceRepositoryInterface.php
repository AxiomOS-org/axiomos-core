<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; interface LedgerBalanceRepositoryInterface { public function upsertLineBalance(array $scope, string $debit, string $credit): void; public function trialBalance(string $companyId): Collection; }

