<?php
declare(strict_types=1);
namespace Modules\Accounting\Infrastructure\Persistence;
use Illuminate\Support\Collection; use Modules\Accounting\Domain\Models\Dimension; use Modules\Accounting\Domain\Repositories\Contracts\DimensionRepositoryInterface; final class EloquentDimensionRepository implements DimensionRepositoryInterface { public function byCompanyAndType(string $companyId,string $type): Collection { return Dimension::query()->where('company_id',$companyId)->where('dimension_type',$type)->orderBy('code')->get(); } }

