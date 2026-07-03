<?php
declare(strict_types=1);
namespace Modules\Inventory\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class InventoryCrudWebController {
    private const PAGES = [
        'warehouse' => ['title' => 'Warehouse', 'api' => '/api/inventory/warehouse', 'columns' => array (
  0 => 'company_id',
  1 => 'code',
  2 => 'name',
  3 => 'status',
)],
        'item' => ['title' => 'Item', 'api' => '/api/inventory/item', 'columns' => array (
  0 => 'sku',
  1 => 'name',
  2 => 'unit',
  3 => 'status',
)],
        'stock-movement' => ['title' => 'Stock Movement', 'api' => '/api/inventory/stock-movement', 'columns' => array (
  0 => 'item_id',
  1 => 'movement_type',
  2 => 'quantity',
  3 => 'reference',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Inventory admin page not found.', Response::HTTP_NOT_FOUND);
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