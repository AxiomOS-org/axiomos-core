<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Repositories\Contracts;
use Modules\Projects\Domain\Models\Project;
interface ProjectRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?Project;
    public function create(array $attributes): Project;
    public function update(Project $model, array $attributes): Project;
}