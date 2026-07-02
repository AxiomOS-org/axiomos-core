<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Identity\Domain\Models\ApiToken;

final class ApiAuthenticationService
{
    public function resolveBearerToken(Request $request): ?ApiToken
    {
        $header = (string) $request->headers->get('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $plain = trim(substr($header, 7));
        if ($plain === '') {
            return null;
        }

        return ApiToken::query()
            ->where('token_hash', hash('sha256', $plain))
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();
    }
}
