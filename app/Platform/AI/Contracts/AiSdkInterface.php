<?php

declare(strict_types=1);

namespace App\Platform\AI\Contracts;

use App\Platform\Support\PlatformEntityInterface;

interface AiSdkInterface
{
    public function storeContext(
        PlatformEntityInterface $entity,
        string $contextKey,
        array $context,
        ?array $metadata = null,
        ?string $updatedBy = null,
    ): void;

    public function buildPrompt(string $template, array $variables): string;

    public function redact(string $input): string;
}
