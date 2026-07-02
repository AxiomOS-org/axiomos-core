<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Controllers\Web;

use Illuminate\Http\Request;
use Modules\Authentication\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticationWebController
{
    public function loginPage(Request $request): Response
    {
        return BladeRenderer::render('auth.login', ['title' => 'Login', 'active' => 'login']);
    }

    public function forgotPasswordPage(Request $request): Response
    {
        return BladeRenderer::render('auth.forgot-password', ['title' => 'Forgot Password', 'active' => 'forgot-password']);
    }

    public function resetPasswordPage(Request $request): Response
    {
        return BladeRenderer::render('auth.reset-password', ['title' => 'Reset Password', 'active' => 'reset-password']);
    }

    public function emailVerificationPage(Request $request): Response
    {
        return BladeRenderer::render('auth.email-verification', ['title' => 'Email Verification', 'active' => 'email-verification']);
    }

    public function logout(Request $request): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
