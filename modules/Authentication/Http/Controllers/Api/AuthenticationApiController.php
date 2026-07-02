<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Application\DTOs\LoginDTO;
use Modules\Authentication\Application\DTOs\OAuthTokenRequestDTO;
use Modules\Authentication\Application\DTOs\PasswordChangeDTO;
use Modules\Authentication\Application\Services\ApiAuthenticationService;
use Modules\Authentication\Application\Services\AuthenticationService;
use Modules\Authentication\Application\Services\EmailVerificationService;
use Modules\Authentication\Application\Services\MfaService;
use Modules\Authentication\Application\Services\OAuthService;
use Modules\Authentication\Application\Services\PasswordResetService;
use Modules\Authentication\Application\Services\PasswordService;
use Modules\Authentication\Application\Services\SessionManagerService;
use Modules\Authentication\Http\Requests\EmailVerificationRequestRules;
use Modules\Authentication\Http\Requests\LoginRequestRules;
use Modules\Authentication\Http\Requests\MfaRequestRules;
use Modules\Authentication\Http\Requests\OAuthRequestRules;
use Modules\Authentication\Http\Requests\PasswordChangeRequestRules;
use Modules\Authentication\Http\Requests\PasswordResetRequestRules;
use Modules\Authentication\Http\Requests\PersonalAccessTokenRequestRules;
use Modules\Authentication\Http\Requests\SessionRequestRules;
use Modules\Authentication\Policies\AuthenticationPolicy;
use Modules\Users\Domain\Models\User;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticationApiController extends ApiController
{
    public function __construct(
        private readonly AuthenticationService $auth,
        private readonly PasswordService $passwords,
        private readonly PasswordResetService $passwordResets,
        private readonly EmailVerificationService $emailVerification,
        private readonly MfaService $mfa,
        private readonly SessionManagerService $sessions,
        private readonly ApiAuthenticationService $apiAuth,
        private readonly OAuthService $oauth,
        private readonly AuthenticationPolicy $policy,
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        if (! $this->policy->access()) {
            return $this->message('Forbidden.', Response::HTTP_FORBIDDEN);
        }

        try {
            $validated = $this->validated($request, LoginRequestRules::login());
            return $this->ok($this->auth->login(LoginDTO::fromArray($validated)));
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->message($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $sessionId = (string) $request->request->get('session_id', '');
        if ($sessionId === '') {
            return $this->message('session_id is required.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->auth->logout($sessionId);

        return $this->message('Logged out.');
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, PasswordChangeRequestRules::change());
            $this->passwords->change(PasswordChangeDTO::fromArray($validated));

            return $this->message('Password changed.');
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->message($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, PasswordResetRequestRules::forgot());
            $token = $this->passwordResets->forgot((string) $validated['email']);

            return $this->ok(['reset_requested' => true, 'token' => $token]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, PasswordResetRequestRules::reset());
            $this->passwordResets->reset((string) $validated['token'], (string) $validated['new_password']);

            return $this->message('Password reset successful.');
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->message($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, EmailVerificationRequestRules::verify());
            $this->emailVerification->verify((string) $validated['token']);

            return $this->message('Email verified.');
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->message($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function enableMfa(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, MfaRequestRules::enable());

            return $this->ok($this->mfa->enableTotp((string) $validated['user_id']));
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }

    public function verifyMfa(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, MfaRequestRules::verify());

            return $this->ok(['valid' => $this->mfa->verifyChallenge((string) $validated['user_id'], (string) $validated['code'])]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }

    public function listSessions(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, SessionRequestRules::list());
            $user = User::query()->find((string) $validated['user_id']);
            if ($user === null) {
                return $this->message('User not found.', Response::HTTP_NOT_FOUND);
            }

            $sessions = array_map(
                static fn ($session): array => [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'status' => $session->status,
                    'started_at' => $session->started_at?->toIso8601String(),
                    'expires_at' => $session->expires_at?->toIso8601String(),
                ],
                $this->sessions->listByUser($user),
            );

            return $this->ok(['sessions' => $sessions]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }

    public function revokeSession(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, SessionRequestRules::revoke());
            $this->sessions->revoke((string) $validated['session_id']);

            return $this->message('Session revoked.');
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }

    public function me(Request $request): JsonResponse
    {
        $token = $this->apiAuth->resolveBearerToken($request);
        if ($token === null) {
            return $this->message('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        }

        return $this->ok([
            'identity_id' => $token->identity_id,
            'token_id' => $token->id,
            'token_name' => $token->name,
            'scopes' => $token->scopes ?? [],
        ]);
    }

    public function oauthToken(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, OAuthRequestRules::token());
            $payload = $this->oauth->issueToken(OAuthTokenRequestDTO::fromArray($validated));

            return $this->ok($payload);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (RuntimeException $exception) {
            return $this->message($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function personalAccessToken(Request $request): JsonResponse
    {
        try {
            $validated = $this->validated($request, PersonalAccessTokenRequestRules::issue());
            $user = User::query()->find((string) $validated['user_id']);
            if ($user === null) {
                return $this->message('User not found.', Response::HTTP_NOT_FOUND);
            }

            $issued = $this->oauth->issuePersonalAccessToken(
                (string) $user->identity_id,
                (string) $validated['name'],
                isset($validated['scopes']) && is_array($validated['scopes']) ? array_values($validated['scopes']) : [],
            );

            return $this->ok($issued, Response::HTTP_CREATED);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }
    }
}
