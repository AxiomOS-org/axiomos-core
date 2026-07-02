<?php

declare(strict_types=1);

namespace Modules\AI\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Platform\AI\Contracts\AiSdkInterface;

final class AIServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.ai', new ModuleInfo('AI', '1.0.0'));
    }

    public function boot(ContainerInterface $container): void
    {
        if ($container->has(AiSdkInterface::class)) {
            $container->instance('platform.ai.sdk', $container->make(AiSdkInterface::class));
        }
    }
}
