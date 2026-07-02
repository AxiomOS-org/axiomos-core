<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
use Modules\Accounting\Application\DTOs\PostingResult;
use Modules\Accounting\Application\DTOs\ReversalRequest;
final class ReversalEngine { public function __construct(private readonly PostingEngine $posting) {} public function reverse(ReversalRequest $request): PostingResult { return $this->posting->reverse($request); } }

