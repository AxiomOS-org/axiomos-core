<?php

declare(strict_types=1);

namespace Modules\Membership\Infrastructure\Persistence\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Modules\Membership\Application\Support\ListQuery;

trait AppliesListQuery
{
    /**
     * @param Builder<\Illuminate\Database\Eloquent\Model> $query
     */
    protected function applyListQuery(Builder $query, ListQuery $listQuery): void
    {
        if ($listQuery->status !== null) {
            $query->where('status', $listQuery->status);
        }

        if ($listQuery->membershipType !== null) {
            $query->where('membership_type', $listQuery->membershipType);
        }

        if ($listQuery->userId !== null) {
            $query->where('user_id', $listQuery->userId);
        }

        if ($listQuery->organizationId !== null) {
            $query->where('organization_id', $listQuery->organizationId);
        }

        if ($listQuery->search !== null) {
            $needle = '%' . $listQuery->search . '%';
            $query->where(static function (Builder $builder) use ($needle): void {
                $builder
                    ->where('membership_type', 'ilike', $needle)
                    ->orWhere('status', 'ilike', $needle);
            });
        }

        $query->orderBy($listQuery->sort, $listQuery->direction);
    }
}
