<?php

declare(strict_types=1);

namespace App\Platform\Bootstrap;

use App\ADT\Extension\ExtensionRegistry;
use App\ADT\Extension\PluginSdk;
use App\ADT\Marketplace\MarketplaceSdk;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Http\Health\HealthChecker;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use App\Platform\Activity\ActivityService;
use App\Platform\AI\AiContextService;
use App\Platform\AI\AiSdk;
use App\Platform\AI\Contracts\AiSdkInterface;
use App\Platform\AI\PromptPolicy;
use App\Platform\AI\PromptPolicyInterface;
use App\Platform\AI\Redaction\RedactionPipeline;
use App\Platform\Approval\ApprovalWorkflowService;
use App\Platform\Attachments\AttachmentService;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Automation\AutomationEngine;
use App\Platform\Automation\AutomationSdk;
use App\Platform\Comments\CommentService;
use App\Platform\Health\PlatformHealthCheck;
use App\Platform\Integration\IntegrationRegistry;
use App\Platform\Integration\IntegrationSdk;
use App\Platform\Notes\NoteService;
use App\Platform\Notifications\NotificationService;
use App\Platform\Support\PlatformEventDispatcherInterface;
use App\Platform\Support\SyncPlatformEventDispatcher;
use App\Platform\Tags\TagService;
use App\Platform\Theme\ThemeResolver;
use App\Platform\Theme\ThemeSdk;
use App\Platform\Timeline\TimelineService;
use App\Platform\Versioning\VersionHistoryService;
use App\Platform\Workflow\WorkflowEngine;
use App\Platform\Workflow\Contracts\WorkflowEngineInterface;
use App\Platform\Workflow\WorkflowSdk;

final class PlatformBootstrap
{
    public static function boot(ContainerInterface $container, string $basePath): void
    {
        $migrations = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Platform' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';

        $container->singleton(PlatformEventDispatcherInterface::class, SyncPlatformEventDispatcher::class);
        $container->singleton(ActivityService::class, ActivityService::class);
        $container->singleton(NotificationService::class, NotificationService::class);
        $container->singleton(AuditTrailService::class, AuditTrailService::class);
        $container->singleton(CommentService::class, CommentService::class);
        $container->singleton(NoteService::class, NoteService::class);
        $container->singleton(TagService::class, TagService::class);
        $container->singleton(VersionHistoryService::class, VersionHistoryService::class);
        $container->singleton(AiContextService::class, AiContextService::class);
        $container->singleton(ApprovalWorkflowService::class, ApprovalWorkflowService::class);
        $container->singleton(AttachmentService::class, AttachmentService::class);
        $container->singleton(TimelineService::class, TimelineService::class);

        $container->singleton(PromptPolicyInterface::class, PromptPolicy::class);
        $container->singleton(RedactionPipeline::class, RedactionPipeline::class);
        $container->singleton(AiSdkInterface::class, static function (ContainerInterface $c): AiSdk {
            return new AiSdk(
                $c->make(AiContextService::class),
                $c->make(PromptPolicyInterface::class),
                $c->make(RedactionPipeline::class),
            );
        });

        $container->singleton(WorkflowEngineInterface::class, static function (ContainerInterface $c): WorkflowEngine {
            return new WorkflowEngine($c->make(ApprovalWorkflowService::class));
        });
        $container->singleton(WorkflowSdk::class, static function (ContainerInterface $c): WorkflowSdk {
            return new WorkflowSdk($c->make(WorkflowEngineInterface::class));
        });

        $container->singleton(AutomationEngine::class, AutomationEngine::class);
        $container->singleton(AutomationSdk::class, static function (ContainerInterface $c): AutomationSdk {
            return new AutomationSdk($c->make(AutomationEngine::class));
        });

        $container->singleton(IntegrationRegistry::class, IntegrationRegistry::class);
        $container->singleton(IntegrationSdk::class, static function (ContainerInterface $c): IntegrationSdk {
            return new IntegrationSdk($c->make(IntegrationRegistry::class));
        });

        $themesPath = $basePath . DIRECTORY_SEPARATOR . 'themes';
        $container->singleton(ThemeResolver::class, static fn (): ThemeResolver => new ThemeResolver($themesPath));
        $container->singleton(ThemeSdk::class, static function (ContainerInterface $c): ThemeSdk {
            return new ThemeSdk($c->make(ThemeResolver::class));
        });

        $pluginsPath = $basePath . DIRECTORY_SEPARATOR . 'plugins';
        $extensionRegistry = new ExtensionRegistry();
        $container->instance(ExtensionRegistry::class, $extensionRegistry);
        $container->singleton(PluginSdk::class, static fn (): PluginSdk => new PluginSdk($pluginsPath, '1.0.0', $extensionRegistry));

        $packagesPath = $basePath . DIRECTORY_SEPARATOR . 'packages';
        $installRoot = $basePath . DIRECTORY_SEPARATOR . 'modules';
        $container->singleton(MarketplaceSdk::class, static fn (): MarketplaceSdk => new MarketplaceSdk($packagesPath, $installRoot, '1.0.0'));

        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ($container->has(HealthChecker::class)) {
            $container->make(HealthChecker::class)->register(new PlatformHealthCheck());
        }

        if ($container->has(PluginSdk::class)) {
            $container->make(PluginSdk::class)->loadAll();
        }
    }
}
