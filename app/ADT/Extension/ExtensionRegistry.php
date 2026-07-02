<?php

declare(strict_types=1);

namespace App\ADT\Extension;

use App\ADT\Extension\Contracts\ExtensionRegistryInterface;
use App\ADT\Extension\Contracts\TemplatePackInterface;
use InvalidArgumentException;

final class ExtensionRegistry implements ExtensionRegistryInterface
{
    /** @var list<TemplatePackInterface> */
    private array $templatePacks = [];

    /** @var array<string, callable> */
    private array $validationRules = [];

    /** @var array<string, callable> */
    private array $transformers = [];

    public function registerTemplatePack(TemplatePackInterface $pack): void
    {
        $this->templatePacks[] = $pack;
    }

    public function registerValidationRule(string $name, callable $rule): void
    {
        $this->validationRules[$name] = $rule;
    }

    public function registerTransformer(string $name, callable $transformer): void
    {
        $this->transformers[$name] = $transformer;
    }

    public function templatePacks(): array
    {
        return $this->templatePacks;
    }

    public function hasValidationRule(string $name): bool
    {
        return isset($this->validationRules[$name]);
    }

    public function validate(string $name, mixed $value): bool
    {
        if (! isset($this->validationRules[$name])) {
            throw new InvalidArgumentException("Validation rule [{$name}] is not registered.");
        }

        return (bool) ($this->validationRules[$name])($value);
    }

    public function transform(string $name, mixed $input): mixed
    {
        if (! isset($this->transformers[$name])) {
            throw new InvalidArgumentException("Transformer [{$name}] is not registered.");
        }

        return ($this->transformers[$name])($input);
    }
}
