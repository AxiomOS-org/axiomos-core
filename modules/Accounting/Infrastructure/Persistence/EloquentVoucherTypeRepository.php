<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\VoucherType; use Modules\Accounting\Domain\Repositories\Contracts\VoucherTypeRepositoryInterface; final class EloquentVoucherTypeRepository implements VoucherTypeRepositoryInterface { public function byCompany(string $companyId): Collection { return VoucherType::query()->where('company_id',$companyId)->orderBy('code')->get(); } }

