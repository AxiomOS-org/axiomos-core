<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Support\Carbon;
use Modules\Identity\Domain\Models\LoginHistory;
use Modules\Users\Domain\Models\User;

final class LoginHistoryService
{
    public function record(User $user, bool $success, ?string $ipAddress = null, ?string $userAgent = null): LoginHistory
    {
        return LoginHistory::query()->create([
            'identity_id' => $user->identity_id,
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'success' => $success,
            'logged_at' => Carbon::now(),
            'status' => $success ? 'success' : 'failed',
        ]);
    }
}
