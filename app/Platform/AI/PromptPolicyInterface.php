<?php

declare(strict_types=1);

namespace App\Platform\AI;

interface PromptPolicyInterface
{
    public function sanitizeTemplate(string $template): string;

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     */
    public function sanitizeVariables(array $variables): array;
}
