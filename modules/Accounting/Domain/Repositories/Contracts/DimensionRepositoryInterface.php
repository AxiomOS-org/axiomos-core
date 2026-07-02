<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Illuminate\Support\Collection; interface DimensionRepositoryInterface { public function byCompanyAndType(string $companyId, string $type): Collection; }

