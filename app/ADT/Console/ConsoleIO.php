<?php

declare(strict_types=1);

namespace App\ADT\Console;

/**
 * Minimal stdin/stdout console adapter for ADT commands.
 */
final class ConsoleIO implements ConsoleIOInterface
{
    public function writeln(string $message = ''): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public function askYesNo(string $prompt): bool
    {
        fwrite(STDOUT, $prompt . ' ');

        $answer = fgets(STDIN);

        if ($answer === false) {
            return false;
        }

        $normalized = strtolower(trim($answer));

        return in_array($normalized, ['y', 'yes'], true);
    }
}
