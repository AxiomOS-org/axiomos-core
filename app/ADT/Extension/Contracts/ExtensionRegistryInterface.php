<?php

declare(strict_types=1);

namespace App\ADT\Extension\Contracts;

interface ExtensionRegistryInterface
{
    public function registerTemplatePack(TemplatePackInterface $pack): void;

    public function registerValidationRule(string $name, callable $rule): void;

    public function registerTransformer(string $name, callable $transformer): void;

    /**
     * @return list<TemplatePackInterface>
     */
    public function templatePacks(): array;

    public function hasValidationRule(string $name): bool;

    public function validate(string $name, mixed $value): bool;

    public function transform(string $name, mixed $input): mixed;
}
