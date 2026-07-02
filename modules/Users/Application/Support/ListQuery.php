<?php

declare(strict_types=1);

namespace Modules\Users\Application\Support;

use Illuminate\Http\Request;

final readonly class ListQuery
{
    private const ALLOWED_SORTS = ['username', 'email', 'display_name', 'status', 'created_at'];

    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public string $sort = 'display_name',
        public string $direction = 'asc',
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $sort = (string) $request->query('sort', 'display_name');
        $direction = strtolower((string) $request->query('direction', 'asc'));

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'display_name';
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
        );
    }

    public static function wantsPagination(Request $request): bool
    {
        foreach (['page', 'search', 'status'] as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }
}
