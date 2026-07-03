<?php
declare(strict_types=1);
namespace Modules\POS\Application\Services;
use Modules\POS\Domain\Repositories\Contracts\PosTerminalRepositoryInterface;
final class PosTerminalService {
    public function __construct(private readonly PosTerminalRepositoryInterface $repository) {}
    public function list(string $companyId): array { return $this->repository->listByCompany($companyId); }
    public function create(array $payload): array { return $this->repository->create($payload)->toArray(); }
    public function update(array $payload): array
    {
        $id = (string) ($payload['id'] ?? '');
        if ($id === '') {
            throw new \InvalidArgumentException('id is required');
        }
        $model = $this->repository->find($id);
        if ($model === null) {
            throw new \RuntimeException('Record not found');
        }
        unset($payload['id']);

        return $this->repository->update($model, $payload)->toArray();
    }
}
