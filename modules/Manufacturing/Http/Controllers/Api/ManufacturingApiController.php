<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Manufacturing\Application\Services\BillOfMaterialService;
use Modules\Manufacturing\Application\Services\WorkOrderService;
use Modules\Manufacturing\Application\Services\ProductionRunService;
use Modules\Manufacturing\Application\Services\ManufacturingPostingService;
use Symfony\Component\HttpFoundation\Response;
final class ManufacturingApiController extends ApiController {
    public function __construct(
        private readonly BillOfMaterialService $billOfMaterialService,
        private readonly WorkOrderService $workOrderService,
        private readonly ProductionRunService $productionRunService,
        private readonly ManufacturingPostingService $posting,
    ) {}


    public function billOfMaterial(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->billOfMaterialService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->billOfMaterialService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->billOfMaterialService->list($companyId)]);
    }
    public function workOrder(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->workOrderService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->workOrderService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->workOrderService->list($companyId)]);
    }
    public function productionRun(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->productionRunService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->productionRunService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->productionRunService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}