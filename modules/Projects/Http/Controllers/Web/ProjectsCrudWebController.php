<?php
declare(strict_types=1);
namespace Modules\Projects\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Projects\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ProjectsCrudWebController {
    private const PAGES = [
        'project' => ['title' => 'Project', 'api' => '/api/projects/project', 'columns' => array (
  0 => 'code',
  1 => 'name',
  2 => 'status',
  3 => 'budget_amount',
)],
        'project-task' => ['title' => 'Project Task', 'api' => '/api/projects/project-task', 'columns' => array (
  0 => 'project_id',
  1 => 'title',
  2 => 'status',
  3 => 'assignee_id',
)],
        'timesheet' => ['title' => 'Timesheet', 'api' => '/api/projects/timesheet', 'columns' => array (
  0 => 'employee_id',
  1 => 'work_date',
  2 => 'hours',
  3 => 'status',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Projects admin page not found.', Response::HTTP_NOT_FOUND);
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