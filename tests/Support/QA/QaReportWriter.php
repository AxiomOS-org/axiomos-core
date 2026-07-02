<?php

declare(strict_types=1);

namespace Tests\Support\QA;

/**
 * Writes enterprise QA score reports.
 */
final class QaReportWriter
{
    /**
     * @param array<string, int|float|string> $scores
     */
    public static function write(string $basePath, array $scores): void
    {
        $directory = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reports';

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $payload = [
            'generated_at' => gmdate('c'),
            'scores' => $scores,
        ];

        file_put_contents(
            $directory . DIRECTORY_SEPARATOR . 'qa-scorecard.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        );
    }
}
