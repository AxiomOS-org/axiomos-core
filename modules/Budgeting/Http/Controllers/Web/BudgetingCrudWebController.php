<?php
declare(strict_types=1);
namespace Modules\Budgeting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Budgeting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class BudgetingCrudWebController {
    private const PAGES = [
        'budget-version' => ['title' => 'Budget Version', 'api' => '/api/budgeting/budget-version', 'columns' => array (
  0 => 'company_id',
  1 => 'name',
  2 => 'fiscal_year',
  3 => 'status',
)],
        'budget-line' => ['title' => 'Budget Line', 'api' => '/api/budgeting/budget-line', 'columns' => array (
  0 => 'account_id',
  1 => 'period_label',
  2 => 'amount',
  3 => 'currency',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Budgeting admin page not found.', Response::HTTP_NOT_FOUND);
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