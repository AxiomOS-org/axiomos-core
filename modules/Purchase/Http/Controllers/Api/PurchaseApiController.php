<?php
declare(strict_types=1);
namespace Modules\Purchase\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Purchase\Application\Services\VendorService;
use Modules\Purchase\Application\Services\PurchaseOrderService;
use Modules\Purchase\Application\Services\PurchaseBillService;
use Modules\Purchase\Application\Services\PurchasePostingService;
use Symfony\Component\HttpFoundation\Response;
final class PurchaseApiController extends ApiController {
    public function __construct(
        private readonly VendorService $vendorService,
        private readonly PurchaseOrderService $purchaseOrderService,
        private readonly PurchaseBillService $purchaseBillService,
        private readonly PurchasePostingService $posting,
    ) {}


    public function vendor(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->vendorService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->vendorService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->vendorService->list($companyId)]);
    }
    public function purchaseOrder(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->purchaseOrderService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->purchaseOrderService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->purchaseOrderService->list($companyId)]);
    }
    public function purchaseBill(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->purchaseBillService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->purchaseBillService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->purchaseBillService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}