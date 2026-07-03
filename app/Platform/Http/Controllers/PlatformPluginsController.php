<?php

declare(strict_types=1);

namespace App\Platform\Http\Controllers;

use App\Core\Module\ModuleRegistry;
use App\Platform\Services\PluginManifestReader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PlatformPluginsController
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly PluginManifestReader $reader,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $plugins = $this->registry
            ->enabled()
            ->map(fn ($manifest): array => $this->reader->toApiPayload($manifest))
            ->filter(static fn (array $plugin): bool => ($plugin['frontend'] ?? null) !== null)
            ->sortBy('priority')
            ->values()
            ->all();

        return new JsonResponse(['data' => $plugins]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $manifest = $this->registry->findByName($id) ?? $this->registry->findByUuid($id);

        if ($manifest === null) {
            return new JsonResponse(['message' => 'Plugin not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $this->reader->toApiPayload($manifest)]);
    }
}
