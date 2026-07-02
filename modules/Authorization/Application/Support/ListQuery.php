<?php

declare(strict_types=1);

namespace Modules\Authorization\Application\Support;

use Illuminate\Http\Request;

final readonly class ListQuery
{
    /** @var list<string> */
    private const ALLOWED_SORTS = ['name', 'slug', 'module', 'action', 'status', 'created_at', 'updated_at'];

    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public ?string $organizationId = null,
        public string $sort = 'created_at',
        public string $direction = 'desc',
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $sort = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('direction', 'desc'));
        $search = $request->query('search');
        $status = $request->query('status');
        $organizationId = $request->query('organization_id');

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'created_at';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        return new self(
            page: max(1, (int) $request->query('page', 1)),
            perPage: min(100, max(5, (int) $request->query('per_page', 15))),
            search: is_string($search) && $search !== '' ? $search : null,
            status: is_string($status) && $status !== '' ? $status : null,
            organizationId: is_string($organizationId) && $organizationId !== '' ? $organizationId : null,
            sort: $sort,
            direction: $direction,
        );
    }

    public static function wantsPagination(Request $request): bool
    {
        foreach (['page', 'search', 'status', 'organization_id'] as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }
}
