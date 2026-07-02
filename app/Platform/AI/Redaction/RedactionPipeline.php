<?php

declare(strict_types=1);

namespace App\Platform\AI\Redaction;

final class RedactionPipeline
{
    /** @var list<string> */
    private array $patterns = [
        '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i',
        '/\b\d{3}-\d{2}-\d{4}\b/',
        '/\bsk-[a-zA-Z0-9]{16,}\b/',
        '/\bBearer\s+[A-Za-z0-9\-._~+\/]+=*/i',
    ];

    public function redact(string $input): string
    {
        $output = $input;

        foreach ($this->patterns as $pattern) {
            $output = (string) preg_replace($pattern, '[REDACTED]', $output);
        }

        return $output;
    }
}
