<?php

declare(strict_types=1);

namespace Modules\Authentication\Providers;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Authentication\Application\Services\ApiAuthenticationService;
use Modules\Authentication\Application\Services\AuthenticationPlatformHooks;
use Modules\Authentication\Application\Services\AuthenticationService;
use Modules\Authentication\Application\Services\EmailVerificationService;
use Modules\Authentication\Application\Services\LoginHistoryService;
use Modules\Authentication\Application\Services\MfaService;
use Modules\Authentication\Application\Services\OAuthService;
use Modules\Authentication\Application\Services\PasswordResetService;
use Modules\Authentication\Application\Services\PasswordService;
use Modules\Authentication\Application\Services\RateLimitService;
use Modules\Authentication\Application\Services\SecurityEventService;
use Modules\Authentication\Application\Services\SessionManagerService;
use Modules\Authentication\Application\Services\TrustedDeviceService;
use Modules\Authentication\Database\Seeders\AuthenticationDemoSeeder;
use Modules\Authentication\Domain\Models\AuthCredential;
use Modules\Authentication\Domain\Repositories\Contracts\CredentialRepositoryInterface;
use Modules\Authentication\Domain\Repositories\Contracts\PasswordPolicyRepositoryInterface;
use Modules\Authentication\Http\Controllers\Api\AuthenticationApiController;
use Modules\Authentication\Http\Controllers\Web\AuthenticationWebController;
use Modules\Authentication\Infrastructure\Persistence\EloquentCredentialRepository;
use Modules\Authentication\Infrastructure\Persistence\EloquentPasswordPolicyRepository;
use Modules\Authentication\Policies\AuthenticationPolicy;

final class AuthenticationServiceProvider extends ModuleServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->instance('module.authentication', new ModuleInfo('Authentication', '1.0.0'));

        $container->singleton(CredentialRepositoryInterface::class, EloquentCredentialRepository::class);
        $container->singleton(PasswordPolicyRepositoryInterface::class, EloquentPasswordPolicyRepository::class);

        $container->singleton(AuthenticationPlatformHooks::class, AuthenticationPlatformHooks::class);
        $container->singleton(SecurityEventService::class, SecurityEventService::class);
        $container->singleton(RateLimitService::class, RateLimitService::class);
        $container->singleton(LoginHistoryService::class, LoginHistoryService::class);
        $container->singleton(SessionManagerService::class, SessionManagerService::class);
        $container->singleton(TrustedDeviceService::class, TrustedDeviceService::class);
        $container->singleton(PasswordService::class, PasswordService::class);
        $container->singleton(PasswordResetService::class, PasswordResetService::class);
        $container->singleton(EmailVerificationService::class, EmailVerificationService::class);
        $container->singleton(MfaService::class, MfaService::class);
        $container->singleton(ApiAuthenticationService::class, ApiAuthenticationService::class);
        $container->singleton(OAuthService::class, OAuthService::class);
        $container->singleton(AuthenticationService::class, AuthenticationService::class);

        $container->singleton(AuthenticationPolicy::class, AuthenticationPolicy::class);
        $container->singleton(AuthenticationApiController::class, AuthenticationApiController::class);
        $container->singleton(AuthenticationWebController::class, AuthenticationWebController::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);

        if ((getenv('APP_ENV') ?: 'production') !== 'production' && AuthCredential::query()->count() === 0) {
            (new AuthenticationDemoSeeder())->run();
        }

        if (! $container->has(Router::class)) {
            return;
        }

        $router = $container->make(Router::class);
        $registrar = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes.php';

        if (is_callable($registrar)) {
            $registrar($router, $container);
        }
    }
}
