<?php
declare(strict_types=1);
namespace Modules\Sales\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Sales\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class SalesCrudWebController {
    private const PAGES = [
        'customer' => ['title' => 'Customer', 'api' => '/api/sales/customer', 'columns' => array (
  0 => 'name',
  1 => 'email',
  2 => 'phone',
  3 => 'status',
)],
        'sales-order' => ['title' => 'Sales Order', 'api' => '/api/sales/sales-order', 'columns' => array (
  0 => 'order_number',
  1 => 'status',
  2 => 'total_amount',
  3 => 'currency',
)],
        'sales-invoice' => ['title' => 'Sales Invoice', 'api' => '/api/sales/sales-invoice', 'columns' => array (
  0 => 'status',
  1 => 'total_amount',
  2 => 'currency',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Sales admin page not found.', Response::HTTP_NOT_FOUND);
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