<?php

declare(strict_types=1);

namespace Modules\Identity\Application\Support;

use Illuminate\Http\Request;

/**
 * Standard list query parameters for identity resources.
 */
final readonly class ListQuery
{
    private const ALLOWED_SORTS = ['display_name', 'name', 'code', 'status', 'created_at', 'updated_at', 'logged_at', 'last_seen_at', 'started_at'];

    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public string $sort = 'created_at',
        public string $direction = 'desc',
        public ?string $organizationId = null,
        public ?string $identityId = null,
        public ?string $teamId = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $sort = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('direction', 'desc'));

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'created_at';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
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
            identityId: self::optionalUuid($request->query('identity_id')),
            teamId: self::optionalUuid($request->query('team_id')),
        );
    }

    public static function wantsPagination(Request $request): bool
    {
        foreach (['page', 'search', 'status', 'organization_id', 'identity_id', 'team_id'] as $key) {
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
