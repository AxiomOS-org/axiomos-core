<?php

declare(strict_types=1);

namespace App\ADT\Extension\Contracts;

interface TemplatePackInterface
{
    public function name(): string;

    public function version(): string;

    /**
     * @return list<string>
     */
    public function templates(): array;

    public function render(string $template, array $context = []): string;
}
