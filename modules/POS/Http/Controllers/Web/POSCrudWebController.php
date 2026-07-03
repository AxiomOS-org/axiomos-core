<?php
declare(strict_types=1);
namespace Modules\POS\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\POS\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class POSCrudWebController {
    private const PAGES = [
        'pos-terminal' => ['title' => 'Pos Terminal', 'api' => '/api/pos/pos-terminal', 'columns' => array (
  0 => 'company_id',
  1 => 'code',
  2 => 'name',
  3 => 'status',
)],
        'pos-session' => ['title' => 'Pos Session', 'api' => '/api/pos/pos-session', 'columns' => array (
  0 => 'terminal_id',
  1 => 'opened_at',
  2 => 'closed_at',
  3 => 'status',
)],
        'pos-order' => ['title' => 'Pos Order', 'api' => '/api/pos/pos-order', 'columns' => array (
  0 => 'status',
  1 => 'total_amount',
  2 => 'currency',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('POS admin page not found.', Response::HTTP_NOT_FOUND);
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