<?php
declare(strict_types=1);
namespace Modules\Sales\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Sales\Application\Services\CustomerService;
use Modules\Sales\Application\Services\SalesOrderService;
use Modules\Sales\Application\Services\SalesInvoiceService;
use Modules\Sales\Application\Services\SalesPostingService;
use Symfony\Component\HttpFoundation\Response;
final class SalesApiController extends ApiController {
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly SalesOrderService $salesOrderService,
        private readonly SalesInvoiceService $salesInvoiceService,
        private readonly SalesPostingService $posting,
    ) {}


    public function customer(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->customerService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->customerService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->customerService->list($companyId)]);
    }
    public function salesOrder(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->salesOrderService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->salesOrderService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->salesOrderService->list($companyId)]);
    }
    public function salesInvoice(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->salesInvoiceService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->salesInvoiceService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->salesInvoiceService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}