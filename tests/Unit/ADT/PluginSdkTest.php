<?php

declare(strict_types=1);

namespace Tests\Unit\ADT;

use App\ADT\Extension\ExtensionRegistry;
use App\ADT\Extension\PluginManifest;
use App\ADT\Extension\PluginSdk;
use PHPUnit\Framework\TestCase;

final class PluginSdkTest extends TestCase
{
    public function test_it_loads_sample_plugin_extensions(): void
    {
        $basePath = dirname(__DIR__, 3);
        $registry = new ExtensionRegistry();
        $sdk = new PluginSdk($basePath . DIRECTORY_SEPARATOR . 'plugins', '1.0.0', $registry);

        self::assertSame(1, $sdk->loadAll());
        self::assertNotEmpty($registry->templatePacks());
        self::assertTrue($registry->hasValidationRule('non-empty'));
        self::assertSame('HELLO', $registry->transform('uppercase', 'hello'));
    }

    public function test_capability_negotiation(): void
    {
        $manifest = new PluginManifest('Sample', '1.0.0', 'Plugins\\SamplePlugin\\SamplePluginProvider', ['templates'], '1.0.0', '/tmp');
        $sdk = new PluginSdk('/tmp', '1.0.0', new ExtensionRegistry());

        self::assertTrue($sdk->negotiateCapabilities($manifest, ['templates']));
        self::assertFalse($sdk->negotiateCapabilities($manifest, ['marketplace']));
    }
}
