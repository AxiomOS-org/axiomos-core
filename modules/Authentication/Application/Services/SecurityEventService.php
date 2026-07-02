<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Modules\Authentication\Domain\Models\AuthSecurityEvent;

final class SecurityEventService
{
    public function __construct(private readonly AuthenticationPlatformHooks $platform)
    {
    }

    /**
     * @param array<string, mixed> $metadata
     * @param array<string, mixed>|null $geo
     */
    public function record(
        string $eventType,
        ?string $userId,
        ?string $ipAddress,
        ?string $userAgent,
        array $metadata = [],
        string $severity = 'info',
        ?array $geo = null,
    ): AuthSecurityEvent {
        $event = AuthSecurityEvent::query()->create([
            'event_type' => $eventType,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'geo' => $geo,
            'metadata' => $metadata,
            'severity' => $severity,
            'status' => 'recorded',
        ]);

        $this->platform->onCreated($event);

        return $event;
    }
}
