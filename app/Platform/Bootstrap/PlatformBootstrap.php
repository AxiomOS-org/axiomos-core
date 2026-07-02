<?php

declare(strict_types=1);

namespace App\Platform\Bootstrap;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Http\Health\HealthChecker;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use App\Platform\Activity\ActivityService;
use App\Platform\Attachments\AttachmentService;
use App\Platform\AI\AiContextService;
use App\Platform\Approval\ApprovalWorkflowService;
use App\Platform\Audit\AuditTrailService;
use App\Platform\Comments\CommentService;
use App\Platform\Notes\NoteService;
use App\Platform\Notifications\NotificationService;
use App\Platform\Support\PlatformEventDispatcherInterface;
use App\Platform\Support\SyncPlatformEventDispatcher;
use App\Platform\Tags\TagService;
use App\Platform\Timeline\TimelineService;
use App\Platform\Versioning\VersionHistoryService;
use App\Platform\Health\PlatformHealthCheck;

final class PlatformBootstrap
{
    public static function boot(ContainerInterface $container, string $basePath): void
    {
        $migrations = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Platform' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';

        // Register platform services. They are resolved lazily when other
        // modules/controllers request them.
        $container->singleton(
            PlatformEventDispatcherInterface::class,
            SyncPlatformEventDispatcher::class,
        );
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

        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ($container->has(HealthChecker::class)) {
            $container->make(HealthChecker::class)->register(new PlatformHealthCheck());
        }
    }
}

