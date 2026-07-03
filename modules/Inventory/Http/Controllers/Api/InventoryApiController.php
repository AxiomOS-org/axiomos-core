<?php
declare(strict_types=1);
namespace Modules\Inventory\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Inventory\Application\Services\WarehouseService;
use Modules\Inventory\Application\Services\ItemService;
use Modules\Inventory\Application\Services\StockMovementService;
use Symfony\Component\HttpFoundation\Response;
final class InventoryApiController extends ApiController {
    public function __construct(
        private readonly WarehouseService $warehouseService,
        private readonly ItemService $itemService,
        private readonly StockMovementService $stockMovementService,
    ) {}


    public function warehouse(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->warehouseService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->warehouseService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->warehouseService->list($companyId)]);
    }
    public function item(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->itemService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->itemService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->itemService->list($companyId)]);
    }
    public function stockMovement(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->stockMovementService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->stockMovementService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->stockMovementService->list($companyId)]);
    }
}