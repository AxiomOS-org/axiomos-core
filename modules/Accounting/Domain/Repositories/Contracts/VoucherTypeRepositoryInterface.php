<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; interface VoucherTypeRepositoryInterface { public function byCompany(string $companyId): Collection; }

