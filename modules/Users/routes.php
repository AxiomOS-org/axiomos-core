<?php

declare(strict_types=1);

use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Users\Http\Controllers\Api\UserApiController;
use Modules\Users\Http\Controllers\Web\UserWebController;

return static function (Router $router, ContainerInterface $container): void {
    $users = $container->make(UserApiController::class);

    $router->get('/api/users', static fn (Request $request) => $users->index($request));
    $router->post('/api/users', static fn (Request $request) => $users->store($request));
    $router->get('/api/users/{id}', static fn (Request $request, string $id) => $users->show($request, $id));
    $router->put('/api/users/{id}', static fn (Request $request, string $id) => $users->update($request, $id));
    $router->delete('/api/users/{id}', static fn (Request $request, string $id) => $users->destroy($request, $id));

    $web = $container->make(UserWebController::class);
    $router->get('/users', static fn (Request $request) => $web->index($request));
};
