<?php

declare(strict_types=1);

namespace Modules\Organization\Infrastructure\Persistence\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Modules\Organization\Application\Support\ListQuery;

trait AppliesListQuery
{
    /**
     * @param Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param list<string>                                 $searchColumns
     */
    protected function applyListQuery(Builder $query, ListQuery $listQuery, array $searchColumns): void
    {
        if ($listQuery->search !== null) {
            $needle = '%' . $listQuery->search . '%';

            $query->where(function (Builder $builder) use ($searchColumns, $needle): void {
                foreach ($searchColumns as $index => $column) {
                    if ($index === 0) {
                        $builder->where($column, 'ilike', $needle);
                    } else {
                        $builder->orWhere($column, 'ilike', $needle);
                    }
                }
            });
        }

        if ($listQuery->status !== null) {
            $query->where('status', $listQuery->status);
        }

        $query->orderBy($listQuery->sort, $listQuery->direction);
    }
}

