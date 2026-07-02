<?php

declare(strict_types=1);

namespace Modules\Organization\Application\Support;

use Illuminate\Http\Request;

/**
 * Standard list query parameters for paginated ERP tables.
 */
final readonly class ListQuery
{
    private const ALLOWED_SORTS = ['name', 'code', 'status', 'created_at', 'updated_at'];

    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public string $sort = 'name',
        public string $direction = 'asc',
        public ?string $organizationId = null,
        public ?string $companyId = null,
        public ?string $branchId = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $sort = (string) $request->query('sort', 'name');
        $direction = strtolower((string) $request->query('direction', 'asc'));

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'name';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $search = $request->query('search');
        $status = $request->query('status');

        return new self(
            page: max(1, (int) $request->query('page', 1)),
            perPage: min(100, max(5, (int) $request->query('per_page', 15))),
            search: is_string($search) && $search !== '' ? $search : null,
            status: is_string($status) && $status !== '' ? $status : null,
            sort: $sort,
            direction: $direction,
            organizationId: self::optionalUuid($request->query('organization_id')),
            companyId: self::optionalUuid($request->query('company_id')),
            branchId: self::optionalUuid($request->query('branch_id')),
        );
    }

    public static function wantsPagination(Request $request): bool
    {
        foreach (['page', 'search', 'status', 'organization_id', 'company_id', 'branch_id'] as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }

    private static function optionalUuid(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
