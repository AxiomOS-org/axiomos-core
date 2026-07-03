<?php
declare(strict_types=1);
namespace Modules\Purchase\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Purchase\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class PurchaseCrudWebController {
    private const PAGES = [
        'vendor' => ['title' => 'Vendor', 'api' => '/api/purchase/vendor', 'columns' => array (
  0 => 'name',
  1 => 'email',
  2 => 'phone',
  3 => 'status',
)],
        'purchase-order' => ['title' => 'Purchase Order', 'api' => '/api/purchase/purchase-order', 'columns' => array (
  0 => 'order_number',
  1 => 'status',
  2 => 'total_amount',
  3 => 'currency',
)],
        'purchase-bill' => ['title' => 'Purchase Bill', 'api' => '/api/purchase/purchase-bill', 'columns' => array (
  0 => 'status',
  1 => 'total_amount',
  2 => 'currency',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Purchase admin page not found.', Response::HTTP_NOT_FOUND);
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