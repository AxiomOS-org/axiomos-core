<?php
declare(strict_types=1);
namespace Modules\Reporting\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Reporting\Application\Services\ReportDefinitionService;
use Modules\Reporting\Application\Services\ReportSnapshotService;
use Modules\Reporting\Application\Services\ReportingDashboardService;
use Symfony\Component\HttpFoundation\Response;
final class ReportingApiController extends ApiController {
    public function __construct(
        private readonly ReportDefinitionService $reportDefinitionService,
        private readonly ReportSnapshotService $reportSnapshotService,
        private readonly ReportingDashboardService $reporting,
    ) {}


    public function reportDefinition(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->reportDefinitionService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->reportDefinitionService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->reportDefinitionService->list($companyId)]);
    }
    public function reportSnapshot(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->reportSnapshotService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->reportSnapshotService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->reportSnapshotService->list($companyId)]);
    }
    public function dashboard(Request $request): Response {
        $companyId = $this->companyId($request);
        return $this->ok(['data' => ['company_id' => $companyId, 'reports' => $this->reporting->dashboard($companyId)]]);
    }

}