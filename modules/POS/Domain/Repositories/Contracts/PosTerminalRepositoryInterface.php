<?php
declare(strict_types=1);
namespace Modules\POS\Domain\Repositories\Contracts;
use Modules\POS\Domain\Models\PosTerminal;
interface PosTerminalRepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string $companyId): array;
    public function find(string $id): ?PosTerminal;
    public function create(array $attributes): PosTerminal;
    public function update(PosTerminal $model, array $attributes): PosTerminal;
}