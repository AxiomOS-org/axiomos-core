<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Authentication\Http\Controllers\Api\AuthenticationApiController;
use Modules\Authentication\Http\Controllers\Web\AuthenticationWebController;

return static function (Router $router, ContainerInterface $container): void {
    $api = $container->make(AuthenticationApiController::class);

    $router->post('/api/auth/login', static fn (Request $request) => $api->login($request));
    $router->post('/api/auth/logout', static fn (Request $request) => $api->logout($request));
    $router->post('/api/auth/password/change', static fn (Request $request) => $api->changePassword($request));
    $router->post('/api/auth/password/forgot', static fn (Request $request) => $api->forgotPassword($request));
    $router->post('/api/auth/password/reset', static fn (Request $request) => $api->resetPassword($request));
    $router->post('/api/auth/email/verify', static fn (Request $request) => $api->verifyEmail($request));
    $router->post('/api/auth/mfa/enable', static fn (Request $request) => $api->enableMfa($request));
    $router->post('/api/auth/mfa/verify', static fn (Request $request) => $api->verifyMfa($request));
    $router->get('/api/auth/sessions', static fn (Request $request) => $api->listSessions($request));
    $router->post('/api/auth/sessions', static fn (Request $request) => $api->revokeSession($request));
    $router->get('/api/auth/me', static fn (Request $request) => $api->me($request));
    $router->post('/api/auth/oauth/token', static fn (Request $request) => $api->oauthToken($request));
    $router->post('/api/auth/personal-access-tokens', static fn (Request $request) => $api->personalAccessToken($request));

    $web = $container->make(AuthenticationWebController::class);
    $router->get('/login', static fn (Request $request) => $web->loginPage($request));
    $router->post('/logout', static fn (Request $request) => $web->logout($request));
    $router->get('/forgot-password', static fn (Request $request) => $web->forgotPasswordPage($request));
    $router->get('/reset-password', static fn (Request $request) => $web->resetPasswordPage($request));
    $router->get('/email-verification', static fn (Request $request) => $web->emailVerificationPage($request));
};
