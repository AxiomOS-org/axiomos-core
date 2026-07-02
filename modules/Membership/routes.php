<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Membership\Http\Controllers\Api\MembershipApiController;
use Modules\Membership\Http\Controllers\Web\MembershipWebController;

return static function (Router $router, ContainerInterface $container): void {
    $memberships = $container->make(MembershipApiController::class);

    $router->get('/api/memberships', static fn (Request $request) => $memberships->index($request));
    $router->post('/api/memberships', static fn (Request $request) => $memberships->store($request));
    $router->get('/api/memberships/{id}', static fn (Request $request, string $id) => $memberships->show($request, $id));
    $router->put('/api/memberships/{id}', static fn (Request $request, string $id) => $memberships->update($request, $id));
    $router->delete('/api/memberships/{id}', static fn (Request $request, string $id) => $memberships->destroy($request, $id));

    $web = $container->make(MembershipWebController::class);
    $router->get('/memberships', static fn (Request $request) => $web->index($request));
};
