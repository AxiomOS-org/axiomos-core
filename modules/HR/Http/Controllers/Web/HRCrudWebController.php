<?php
declare(strict_types=1);
namespace Modules\HR\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\HR\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class HRCrudWebController {
    private const PAGES = [
        'employee' => ['title' => 'Employee', 'api' => '/api/hr/employee', 'columns' => array (
  0 => 'employee_code',
  1 => 'full_name',
  2 => 'email',
  3 => 'status',
)],
        'attendance-record' => ['title' => 'Attendance Record', 'api' => '/api/hr/attendance-record', 'columns' => array (
  0 => 'employee_id',
  1 => 'work_date',
  2 => 'status',
  3 => 'hours_worked',
)],
        'payroll-run' => ['title' => 'Payroll Run', 'api' => '/api/hr/payroll-run', 'columns' => array (
  0 => 'status',
  1 => 'total_amount',
  2 => 'currency',
  3 => 'journal_id',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('HR admin page not found.', Response::HTTP_NOT_FOUND);
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