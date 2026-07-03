<?php
declare(strict_types=1);
namespace Modules\Projects\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Projects\Application\Services\ProjectService;
use Modules\Projects\Application\Services\ProjectTaskService;
use Modules\Projects\Application\Services\TimesheetService;
use Symfony\Component\HttpFoundation\Response;
final class ProjectsApiController extends ApiController {
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly ProjectTaskService $projectTaskService,
        private readonly TimesheetService $timesheetService,
    ) {}


    public function project(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->projectService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->projectService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->projectService->list($companyId)]);
    }
    public function projectTask(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->projectTaskService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->projectTaskService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->projectTaskService->list($companyId)]);
    }
    public function timesheet(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->timesheetService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->timesheetService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->timesheetService->list($companyId)]);
    }
}