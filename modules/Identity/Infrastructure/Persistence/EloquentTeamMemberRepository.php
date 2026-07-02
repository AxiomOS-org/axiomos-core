<?php

declare(strict_types=1);

namespace Modules\Identity\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Domain\Models\TeamMember;
use Modules\Identity\Domain\Repositories\Contracts\TeamMemberRepositoryInterface;
use Modules\Identity\Infrastructure\Persistence\Concerns\AppliesListQuery;

final class EloquentTeamMemberRepository implements TeamMemberRepositoryInterface
{
    use AppliesListQuery;

    public function all(?string $teamId = null, ?string $identityId = null): Collection
    {
        $query = TeamMember::query()
            ->with(['team', 'identity'])
            ->orderByDesc('created_at');

        if ($teamId !== null) {
            $query->where('team_id', $teamId);
        }

        if ($identityId !== null) {
            $query->where('identity_id', $identityId);
        }

        return $query->get();
    }

    public function paginate(ListQuery $query): LengthAwarePaginator
    {
        $builder = TeamMember::query()->with(['team', 'identity']);

        if ($query->teamId !== null) {
            $builder->where('team_id', $query->teamId);
        }

        if ($query->identityId !== null) {
            $builder->where('identity_id', $query->identityId);
        }

        $this->applyListQuery($builder, $query, ['role', 'team_id', 'identity_id']);

        return $builder->paginate($query->perPage, ['*'], 'page', $query->page);
    }

    public function find(string $id): ?TeamMember
    {
        return TeamMember::query()->with(['team', 'identity'])->find($id);
    }

    public function create(array $attributes): TeamMember
    {
        return TeamMember::query()->create($attributes);
    }

    public function update(TeamMember $member, array $attributes): TeamMember
    {
        $member->fill($attributes);
        $member->save();

        return $member->refresh();
    }

    public function delete(TeamMember $member): void
    {
        $member->delete();
    }
}
