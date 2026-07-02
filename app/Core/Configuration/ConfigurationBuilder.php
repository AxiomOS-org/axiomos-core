<?php

declare(strict_types=1);

namespace App\Core\Configuration;

use App\Core\Configuration\Contracts\ConfigurationValidatorInterface;
use App\Core\Configuration\Loaders\DatabaseConfigurationLoader;
use App\Core\Configuration\Loaders\EnvironmentConfigurationLoader;
use App\Core\Configuration\Loaders\FileConfigurationLoader;
use App\Core\Configuration\Loaders\ModuleConfigurationLoader;
use App\Core\Configuration\Loaders\PluginConfigurationLoader;
use App\Core\Configuration\Loaders\RuntimeConfigurationLoader;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Fluent builder for a production {@see ConfigurationManager}.
 */
final class ConfigurationBuilder
{
    private string $basePath;

    private ?Dispatcher $events = null;

    private ?string $cachePath = null;

    /** @var (callable(): array<string, mixed>)|null */
    private $databaseResolver = null;

    private ?ConfigurationValidatorInterface $validator = null;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? getcwd() ?: '.';
    }

    public static function create(?string $basePath = null): self
    {
        return new self($basePath);
    }

    public function withBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function withEventDispatcher(Dispatcher $events): self
    {
        $this->events = $events;

        return $this;
    }

    public function withCachePath(string $cachePath): self
    {
        $this->cachePath = $cachePath;

        return $this;
    }

    /**
     * @param callable(): array<string, mixed> $resolver
     */
    public function withDatabaseResolver(callable $resolver): self
    {
        $this->databaseResolver = $resolver;

        return $this;
    }

    public function withValidator(ConfigurationValidatorInterface $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    public function build(): ConfigurationManager
    {
        $runtimeLoader = new RuntimeConfigurationLoader();

        return new ConfigurationManager(
            loaders: [
                new FileConfigurationLoader($this->path('config')),
                new EnvironmentConfigurationLoader($this->path('.env')),
                new DatabaseConfigurationLoader($this->databaseResolver),
                new ModuleConfigurationLoader($this->path('modules')),
                new PluginConfigurationLoader($this->path('plugins')),
                $runtimeLoader,
            ],
            validator: $this->validator ?? new ConfigurationValidator(),
            runtimeLoader: $runtimeLoader,
            events: $this->events,
            defaultCachePath: $this->cachePath,
        );
    }

    private function path(string $segment): string
    {
        return rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $segment;
    }
}
