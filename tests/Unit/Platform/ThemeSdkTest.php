<?php

declare(strict_types=1);

namespace Tests\Unit\Platform;

use App\Platform\AI\AiSdk;
use App\Platform\AI\PromptPolicy;
use App\Platform\AI\Redaction\RedactionPipeline;
use App\Platform\Theme\ThemeResolver;
use App\Platform\Theme\ThemeSdk;
use PHPUnit\Framework\TestCase;

final class ThemeSdkTest extends TestCase
{
    public function test_it_renders_default_theme_layout(): void
    {
        $basePath = dirname(__DIR__, 3);
        $resolver = new ThemeResolver($basePath . DIRECTORY_SEPARATOR . 'themes');
        $sdk = new ThemeSdk($resolver);

        $html = $sdk->renderLayout('app', ['title' => 'Test', 'content' => '<p>OK</p>']);

        self::assertStringContainsString('Test', $html);
        self::assertStringContainsString('<p>OK</p>', $html);
        self::assertContains('Default', $sdk->availableThemes());
    }
}

final class AiSdkRedactionTest extends TestCase
{
    public function test_it_redacts_sensitive_values_in_prompts(): void
    {
        $contextService = $this->createMock(\App\Platform\AI\AiContextService::class);
        $sdk = new AiSdk($contextService, new PromptPolicy(), new RedactionPipeline());

        $prompt = $sdk->buildPrompt('Contact {{email}} token {{token}}', [
            'email' => 'user@example.com',
            'token' => 'Bearer abc.def.ghi',
        ]);

        self::assertStringNotContainsString('user@example.com', $prompt);
        self::assertStringContainsString('[REDACTED]', $prompt);
    }
}
