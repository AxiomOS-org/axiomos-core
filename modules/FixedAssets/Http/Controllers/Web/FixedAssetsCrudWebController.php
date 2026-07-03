<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\FixedAssets\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class FixedAssetsCrudWebController {
    private const PAGES = [
        'fixed-asset' => ['title' => 'Fixed Asset', 'api' => '/api/assets/fixed-asset', 'columns' => array (
  0 => 'name',
  1 => 'status',
  2 => 'acquisition_cost',
  3 => 'currency',
)],
        'depreciation-run' => ['title' => 'Depreciation Run', 'api' => '/api/assets/depreciation-run', 'columns' => array (
  0 => 'status',
  1 => 'total_amount',
  2 => 'currency',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('FixedAssets admin page not found.', Response::HTTP_NOT_FOUND);
        }
        return BladeRenderer::render('crud.index', [
            'title' => $page['title'],
            'active' => $entity,
            'entity' => $entity,
            'entityLabel' => $page['title'],
            'apiBase' => $page['api'],
            'columns' => $page['columns'],
            'fields' => $page['columns'],
        ]);
    }
}