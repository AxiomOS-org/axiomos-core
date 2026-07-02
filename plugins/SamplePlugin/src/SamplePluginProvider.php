<?php

declare(strict_types=1);

namespace Plugins\SamplePlugin;

use App\ADT\Extension\Contracts\ExtensionProviderInterface;
use App\ADT\Extension\Contracts\ExtensionRegistryInterface;
use App\ADT\Extension\Contracts\TemplatePackInterface;

final class SamplePluginProvider implements ExtensionProviderInterface
{
    public function name(): string
    {
        return 'SamplePlugin';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function capabilities(): array
    {
        return ['templates', 'validation', 'transform'];
    }

    public function register(ExtensionRegistryInterface $registry): void
    {
        $registry->registerTemplatePack(new SampleTemplatePack());
        $registry->registerValidationRule('non-empty', static fn (mixed $value): bool => is_string($value) && trim($value) !== '');
        $registry->registerTransformer('uppercase', static fn (mixed $value): string => strtoupper((string) $value));
    }
}

final class SampleTemplatePack implements TemplatePackInterface
{
    public function name(): string
    {
        return 'sample';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function templates(): array
    {
        return ['greeting'];
    }

    public function render(string $template, array $context = []): string
    {
        if ($template !== 'greeting') {
            throw new \InvalidArgumentException("Unknown template: {$template}");
        }

        $name = (string) ($context['name'] ?? 'AxiomOS');

        return "Hello, {$name}!";
    }
}
