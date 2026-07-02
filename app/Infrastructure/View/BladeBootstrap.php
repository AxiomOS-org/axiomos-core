<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * Boots Blade outside a full Laravel application.
 */
final class BladeBootstrap
{
    private static ?Factory $factory = null;

    /**
     * @param list<string> $viewPaths
     */
    public static function boot(string $cachePath, array $viewPaths): Factory
    {
        if (self::$factory !== null) {
            $finder = self::$factory->getFinder();

            foreach ($viewPaths as $viewPath) {
                if (is_dir($viewPath)) {
                    $finder->addLocation($viewPath);
                }
            }

            return self::$factory;
        }

        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        $filesystem = new Filesystem();
        $resolver = new EngineResolver();
        $resolver->register('blade', static fn (): CompilerEngine => new CompilerEngine(
            new BladeCompiler($filesystem, $cachePath),
        ));
        $resolver->register('php', static fn (): \Illuminate\View\Engines\PhpEngine => new \Illuminate\View\Engines\PhpEngine());

        $finder = new FileViewFinder($filesystem, $viewPaths);
        $factory = new Factory($resolver, $finder, new Dispatcher(new Container()));
        $factory->share('appName', 'AxiomOS');

        self::$factory = $factory;

        return $factory;
    }

    public static function factory(): Factory
    {
        if (self::$factory === null) {
            throw new \RuntimeException('Blade has not been booted.');
        }

        return self::$factory;
    }

    public static function reset(): void
    {
        self::$factory = null;
    }
}
