<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Boot\BootManager;
use App\Core\Boot\Support\ProviderModuleBootstrapper;
use App\Core\Configuration\ConfigurationBuilder;
use App\Core\Container\Container;
use App\Core\Event\EventBusBuilder;
use App\Core\Http\Controllers\HealthController;
use App\Core\Http\Controllers\HomeController;
use App\Core\Http\Controllers\MetricsController;
use App\Core\Http\Health\Checks\KernelReadyCheck;
use App\Core\Http\Health\Checks\MemoryCheck;
use App\Core\Http\Health\Checks\ModulesBootedCheck;
use App\Core\Http\Health\HealthChecker;
use App\Core\Kernel\Kernel;
use App\Core\Kernel\KernelManager;
use App\Core\Module\ModuleLoader;
use App\Core\Module\ModuleRegistry;
use App\Core\Module\Support\ClassExistsProviderChecker;
use App\Platform\Bootstrap\PlatformBootstrap;
use App\Infrastructure\Database\DatabaseBootstrap;
use Closure;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Assembles a production-ready {@see HttpKernel}: the AxiomOS kernel, the Laravel
 * router, health checks, controllers and routes, wired in one place.
 *
 * Kept out of the frozen core kernel so HTTP concerns stay in the HTTP layer.
 */
final class HttpKernelFactory
{
    public static function create(string $basePath, ?LoggerInterface $logger = null): HttpKernel
    {
        $logger ??= new NullLogger();
        $environment = (string) (getenv('APP_ENV') ?: 'production');
        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

        $container = new Container();
        $registry = new ModuleRegistry();
        $bootEvents = new IlluminateDispatcher();

        self::bootstrapDatabase($basePath);

        $bootManager = new BootManager(
            loader: new ModuleLoader($modulesPath, new ClassExistsProviderChecker(), null, $bootEvents),
            registry: $registry,
            bootstrapper: new ProviderModuleBootstrapper($container),
            logger: $logger,
            events: $bootEvents,
        );

        $kernel = new Kernel(
            bootManager: $bootManager,
            moduleRegistry: $registry,
            container: $container,
            configuration: ConfigurationBuilder::create($basePath)->build(),
            events: $bootEvents,
            version: '1.0.0',
            environment: $environment,
        );

        $manager = new KernelManager($kernel);

        $health = new HealthChecker(
            new KernelReadyCheck($kernel),
            new ModulesBootedCheck($kernel),
            new MemoryCheck(),
        );

        $container->instance(HealthChecker::class, $health);
        PlatformBootstrap::boot($container, $basePath);

        $router = self::buildRouter();
        $container->instance(Router::class, $router);

        self::registerRoutes($basePath, $router, $manager, $health);

        return new HttpKernel(
            kernel: $manager,
            router: $router,
            events: (new EventBusBuilder())->withLogger($logger)->build(),
            logger: $logger,
        );
    }

    private static function buildRouter(): Router
    {
        $container = new IlluminateContainer();
        $events = new IlluminateDispatcher($container);

        // The router resolves these dispatchers to invoke closure/controller
        // routes; a full app registers them via RoutingServiceProvider.
        $container->singleton(
            CallableDispatcherContract::class,
            static fn (IlluminateContainer $app): CallableDispatcher => new CallableDispatcher($app),
        );
        $container->singleton(
            ControllerDispatcherContract::class,
            static fn (IlluminateContainer $app): ControllerDispatcher => new ControllerDispatcher($app),
        );

        return new Router($events, $container);
    }

    private static function registerRoutes(
        string $basePath,
        Router $router,
        KernelManager $manager,
        HealthChecker $health,
    ): void {
        $registrar = require $basePath . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php';

        if (! $registrar instanceof Closure) {
            return;
        }

        $registrar(
            $router,
            new HomeController(),
            new HealthController($manager, $health),
            new MetricsController($manager),
        );
    }

    private static function bootstrapDatabase(string $basePath): void
    {
        /** @var array<string, mixed> $config */
        $config = require $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

        DatabaseBootstrap::boot($config);
    }
}
