<?php
declare(strict_types=1);
namespace Modules\CRM\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\CRM\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class CRMCrudWebController {
    private const PAGES = [
        'lead' => ['title' => 'Lead', 'api' => '/api/crm/lead', 'columns' => array (
  0 => 'name',
  1 => 'email',
  2 => 'source',
  3 => 'status',
)],
        'opportunity' => ['title' => 'Opportunity', 'api' => '/api/crm/opportunity', 'columns' => array (
  0 => 'title',
  1 => 'stage',
  2 => 'amount',
  3 => 'currency',
)],
        'crm-activity' => ['title' => 'Crm Activity', 'api' => '/api/crm/crm-activity', 'columns' => array (
  0 => 'subject',
  1 => 'activity_type',
  2 => 'status',
  3 => 'due_at',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('CRM admin page not found.', Response::HTTP_NOT_FOUND);
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