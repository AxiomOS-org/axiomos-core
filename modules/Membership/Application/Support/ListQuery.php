<?php

declare(strict_types=1);

namespace Modules\Membership\Application\Support;

use Illuminate\Http\Request;

final readonly class ListQuery
{
    private const ALLOWED_SORTS = ['membership_type', 'status', 'created_at', 'updated_at'];

    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
        public ?string $status = null,
        public ?string $membershipType = null,
        public ?string $userId = null,
        public ?string $organizationId = null,
        public string $sort = 'created_at',
        public string $direction = 'desc',
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
        $membershipType = $request->query('membership_type');
        $userId = $request->query('user_id');
        $organizationId = $request->query('organization_id');

        return new self(
            page: max(1, (int) $request->query('page', 1)),
            perPage: min(100, max(5, (int) $request->query('per_page', 15))),
            search: is_string($search) && $search !== '' ? $search : null,
            status: is_string($status) && $status !== '' ? $status : null,
            membershipType: is_string($membershipType) && $membershipType !== '' ? $membershipType : null,
            userId: is_string($userId) && $userId !== '' ? $userId : null,
            organizationId: is_string($organizationId) && $organizationId !== '' ? $organizationId : null,
            sort: $sort,
            direction: $direction,
        );
    }

    public static function wantsPagination(Request $request): bool
    {
        foreach (['page', 'search', 'status', 'membership_type', 'user_id', 'organization_id'] as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }
}
