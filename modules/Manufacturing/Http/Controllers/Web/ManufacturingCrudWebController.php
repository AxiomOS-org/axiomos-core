<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Manufacturing\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ManufacturingCrudWebController {
    private const PAGES = [
        'bill-of-material' => ['title' => 'Bill Of Material', 'api' => '/api/manufacturing/bill-of-material', 'columns' => array (
  0 => 'company_id',
  1 => 'item_id',
  2 => 'version',
  3 => 'status',
)],
        'work-order' => ['title' => 'Work Order', 'api' => '/api/manufacturing/work-order', 'columns' => array (
  0 => 'bom_id',
  1 => 'order_number',
  2 => 'status',
  3 => 'quantity',
)],
        'production-run' => ['title' => 'Production Run', 'api' => '/api/manufacturing/production-run', 'columns' => array (
  0 => 'work_order_id',
  1 => 'status',
  2 => 'quantity_produced',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Manufacturing admin page not found.', Response::HTTP_NOT_FOUND);
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