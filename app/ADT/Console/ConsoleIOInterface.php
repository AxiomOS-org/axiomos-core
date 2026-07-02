<?php

declare(strict_types=1);

namespace App\ADT\Console;

interface ConsoleIOInterface
{
    public function writeln(string $message = ''): void;

    public function askYesNo(string $prompt): bool;
}
