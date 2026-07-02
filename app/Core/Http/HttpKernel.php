<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Event\Contracts\EventBusInterface;
use App\Core\Http\Events\RequestReceived;
use App\Core\Http\Events\ResponsePrepared;
use App\Core\Kernel\KernelManager;
use App\Core\Kernel\KernelState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Bridges Laravel's HTTP layer with the AxiomOS kernel.
 *
 * Every request follows the documented lifecycle:
 *
 *     Load configuration -> Boot kernel -> Load modules -> Dispatch events -> Return response
 *
 * The kernel boots lazily and is reused once ready, so a long-running runtime
 * (Octane/RoadRunner) boots once while classic FPM boots per process.
 */
final class HttpKernel
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly KernelManager $kernel,
        private readonly Router $router,
        private readonly EventBusInterface $events,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function handle(Request $request): Response
    {
        $startedAt = hrtime(true);

        try {
            $this->bootKernel();

            $this->events->dispatch(new RequestReceived(
                method: $request->getMethod(),
                path: $request->getPathInfo(),
                receivedAt: microtime(true),
            ));

            $this->bindRequest($request);

            $response = $this->router->dispatch($request);

            $this->events->dispatch(new ResponsePrepared(
                statusCode: $response->getStatusCode(),
                durationMs: (hrtime(true) - $startedAt) / 1_000_000,
            ));

            return $response;
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse([
                'kernel' => 'AxiomOS',
                'status' => 'not_found',
                'message' => sprintf('No route matched "%s".', $request->getPathInfo()),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $exception) {
            $this->logger->error('HTTP request failed.', [
                'path' => $request->getPathInfo(),
                'exception' => $exception->getMessage(),
            ]);

            return new JsonResponse([
                'kernel' => 'AxiomOS',
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Flush per-request state after the response is sent (long-running runtimes).
     */
    public function terminate(): void
    {
        $this->kernel->kernel()->container()->flushScoped();
    }

    private function bootKernel(): void
    {
        if ($this->kernel->kernel()->state() !== KernelState::Ready) {
            $this->kernel->boot();
        }
    }

    private function bindRequest(Request $request): void
    {
        $property = new \ReflectionProperty($this->router, 'container');
        $property->setAccessible(true);

        /** @var \Illuminate\Container\Container $container */
        $container = $property->getValue($this->router);
        $container->instance('request', $request);
        $container->instance(Request::class, $request);
    }
}
