<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Authentication\Domain\Models\AuthTrustedDevice;

final class TrustedDeviceService
{
    /**
     * @param array<string, mixed>|null $geo
     */
    public function remember(
        string $userId,
        string $fingerprint,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $geo = null,
    ): AuthTrustedDevice {
        $device = AuthTrustedDevice::query()->where('user_id', $userId)->where('device_fingerprint', $fingerprint)->first();

        if ($device !== null) {
            $device->update([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'geo' => $geo,
                'last_used_at' => Carbon::now(),
                'trusted_until' => Carbon::now()->addDays(30),
            ]);

            return $device->refresh();
        }

        return AuthTrustedDevice::query()->create([
            'user_id' => $userId,
            'device_fingerprint' => $fingerprint,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'geo' => $geo,
            'last_used_at' => Carbon::now(),
            'trusted_until' => Carbon::now()->addDays(30),
            'status' => 'active',
        ]);
    }

    public function isTrusted(string $userId, string $fingerprint): bool
    {
        return AuthTrustedDevice::query()
            ->where('user_id', $userId)
            ->where('device_fingerprint', $fingerprint)
            ->where('trusted_until', '>', Carbon::now())
            ->exists();
    }
}
