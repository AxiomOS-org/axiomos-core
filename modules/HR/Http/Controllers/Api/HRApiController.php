<?php
declare(strict_types=1);
namespace Modules\HR\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\HR\Application\Services\EmployeeService;
use Modules\HR\Application\Services\AttendanceRecordService;
use Modules\HR\Application\Services\PayrollRunService;
use Modules\HR\Application\Services\HRPostingService;
use Symfony\Component\HttpFoundation\Response;
final class HRApiController extends ApiController {
    public function __construct(
        private readonly EmployeeService $employeeService,
        private readonly AttendanceRecordService $attendanceRecordService,
        private readonly PayrollRunService $payrollRunService,
        private readonly HRPostingService $posting,
    ) {}


    public function employee(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->employeeService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->employeeService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->employeeService->list($companyId)]);
    }
    public function attendanceRecord(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->attendanceRecordService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->attendanceRecordService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->attendanceRecordService->list($companyId)]);
    }
    public function payrollRun(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->payrollRunService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->payrollRunService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->payrollRunService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}