<?php

declare(strict_types=1);

namespace App\Platform\AI;

final class PromptPolicy implements PromptPolicyInterface
{
    /** @var list<string> */
    private array $blockedPatterns = [
        '/\bignore\s+previous\s+instructions\b/i',
        '/\bsystem\s+prompt\b/i',
        '/\bapi[_-]?key\b/i',
    ];

    public function sanitizeTemplate(string $template): string
    {
        $sanitized = trim($template);

        foreach ($this->blockedPatterns as $pattern) {
            $sanitized = (string) preg_replace($pattern, '[redacted]', $sanitized);
        }

        return $sanitized;
    }

    public function sanitizeVariables(array $variables): array
    {
        $sanitized = [];

        foreach ($variables as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeTemplate($value);
            } elseif (is_scalar($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = json_encode($value, JSON_THROW_ON_ERROR);
            }
        }

        return $sanitized;
    }
}
