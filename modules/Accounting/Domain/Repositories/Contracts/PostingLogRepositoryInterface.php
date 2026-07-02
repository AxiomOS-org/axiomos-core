<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Repositories\Contracts;
use Modules\Accounting\Domain\Models\PostingLog; interface PostingLogRepositoryInterface { public function findByIdempotencyKey(string $key): ?PostingLog; public function create(array $attributes): PostingLog; public function update(PostingLog $log, array $attributes): PostingLog; }

