<?php

declare(strict_types=1);

namespace Modules\Workflow\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Platform\Workflow\WorkflowSdk;

final class WorkflowServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.workflow', new ModuleInfo('Workflow', '1.0.0'));
    }

    public function boot(ContainerInterface $container): void
    {
        if ($container->has(WorkflowSdk::class)) {
            $container->instance('platform.workflow.sdk', $container->make(WorkflowSdk::class));
        }
    }
}
