<?php
declare(strict_types=1);
namespace Modules\Reporting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Reporting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ReportingCrudWebController {
    private const PAGES = [
        'report-definition' => ['title' => 'Report Definition', 'api' => '/api/reporting/report-definition', 'columns' => array (
  0 => 'code',
  1 => 'name',
  2 => 'report_type',
  3 => 'status',
)],
        'report-snapshot' => ['title' => 'Report Snapshot', 'api' => '/api/reporting/report-snapshot', 'columns' => array (
  0 => 'report_definition_id',
  1 => 'snapshot_date',
  2 => 'status',
  3 => 'payload_json',
)],
    ];
    public function index(Request $request, string $entity): Response {
        $page = self::PAGES[$entity] ?? null;
        if ($page === null) {
            return new Response('Reporting admin page not found.', Response::HTTP_NOT_FOUND);
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