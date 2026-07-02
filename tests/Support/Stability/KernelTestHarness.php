<?php

declare(strict_types=1);

namespace Tests\Support\Stability;

use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use App\Core\Kernel\KernelManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use ReflectionProperty;
use Tests\Support\PostgresFeatureTestCase;

/**
 * Boots a real HttpKernel against PostgreSQL for stability probes.
 */
abstract class KernelTestHarness extends PostgresFeatureTestCase
{
    protected HttpKernel $kernel;

    /** @var array<string, string> */
    protected array $sampleIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = HttpKernelFactory::create($this->basePath);
        $this->warmKernel();
        $this->sampleIds = $this->discoverSampleIds();
    }

    protected function warmKernel(): void
    {
        $this->kernel->handle(Request::create('/health', 'GET'));
    }

    protected function router(): Router
    {
        $property = new ReflectionProperty($this->kernel, 'router');
        $property->setAccessible(true);

        /** @var Router $router */
        $router = $property->getValue($this->kernel);

        return $router;
    }

    protected function kernelManager(): KernelManager
    {
        $property = new ReflectionProperty($this->kernel, 'kernel');
        $property->setAccessible(true);

        /** @var KernelManager $manager */
        $manager = $property->getValue($this->kernel);

        return $manager;
    }

    protected function container(): ContainerInterface
    {
        return $this->kernelManager()->kernel()->container();
    }

  /**
     * @return array<string, string>
     */
    protected function discoverSampleIds(): array
    {
        $ids = [
            'id' => '00000000-0000-4000-8000-000000000099',
            'userId' => '00000000-0000-4000-8000-000000000099',
        ];

        $endpoints = [
            'organization' => '/api/organizations?page=1&per_page=1',
            'company' => '/api/companies?page=1&per_page=1',
            'branch' => '/api/branches?page=1&per_page=1',
            'department' => '/api/departments?page=1&per_page=1',
            'identity' => '/api/identities?page=1&per_page=1',
            'user' => '/api/users?page=1&per_page=1',
            'role' => '/api/security/roles?page=1&per_page=1',
            'permission' => '/api/security/permissions?page=1&per_page=1',
            'team' => '/api/teams?page=1&per_page=1',
        ];

        foreach ($endpoints as $key => $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            if ($response->getStatusCode() !== 200) {
                continue;
            }

            $content = $response->getContent();
            if (! is_string($content)) {
                continue;
            }

            /** @var array<string, mixed> $payload */
            $payload = json_decode($content, true);
            $first = $payload['data'][0]['id'] ?? null;

            if (is_string($first) && $first !== '') {
                $ids[$key] = $first;
                $ids['id'] = $first;
            }
        }

        if (isset($ids['user'])) {
            $ids['userId'] = $ids['user'];
        }

        return $ids;
    }

    protected function substituteRouteUri(string $uri): string
    {
        $resolved = $uri;

        foreach ($this->sampleIds as $parameter => $value) {
            $resolved = str_replace('{' . $parameter . '}', $value, $resolved);
        }

        return $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeJson(string|false $content): array
    {
        self::assertIsString($content);
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
