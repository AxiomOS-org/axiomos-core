<?php
declare(strict_types=1);
namespace Modules\Projects\Domain\Repositories\Contracts;
use Modules\Projects\Domain\Models\ProjectTask;
interface ProjectTaskRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?ProjectTask;
    public function create(array $attributes): ProjectTask;
    public function update(ProjectTask $model, array $attributes): ProjectTask;
}