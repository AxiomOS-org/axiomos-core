<?php
declare(strict_types=1);
namespace Modules\Accounting\Domain\Services\Contracts;
use Modules\Accounting\Application\DTOs\PostingPreview;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Application\DTOs\PostingResult;
use Modules\Accounting\Application\DTOs\ReversalRequest;
interface PostingEngineInterface { public function submit(PostingRequest $request): PostingResult; public function reverse(ReversalRequest $request): PostingResult; public function preview(PostingRequest $request): PostingPreview; }

