<?php

declare(strict_types=1);

namespace App\Platform\AI;

use App\Platform\AI\Contracts\AiSdkInterface;
use App\Platform\AI\Redaction\RedactionPipeline;
use App\Platform\Support\PlatformEntityInterface;

final class AiSdk implements AiSdkInterface
{
    public function __construct(
        private readonly AiContextService $contextService,
        private readonly PromptPolicyInterface $promptPolicy,
        private readonly RedactionPipeline $redactionPipeline,
    ) {
    }

    public function storeContext(
        PlatformEntityInterface $entity,
        string $contextKey,
        array $context,
        ?array $metadata = null,
        ?string $updatedBy = null,
    ): void {
        $this->contextService->upsert(
            $entity,
            $contextKey,
            $this->promptPolicy->sanitizeVariables($context),
            $metadata,
            $updatedBy,
        );
    }

    public function buildPrompt(string $template, array $variables): string
    {
        $safeTemplate = $this->promptPolicy->sanitizeTemplate($template);
        $safeVariables = $this->promptPolicy->sanitizeVariables($variables);

        $prompt = $safeTemplate;

        foreach ($safeVariables as $key => $value) {
            $prompt = str_replace('{{' . $key . '}}', (string) $value, $prompt);
        }

        return $this->redactionPipeline->redact($prompt);
    }

    public function redact(string $input): string
    {
        return $this->redactionPipeline->redact($input);
    }
}
