<?php
declare(strict_types=1);
namespace Modules\POS\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\POS\Application\Services\PosTerminalService;
use Modules\POS\Application\Services\PosSessionService;
use Modules\POS\Application\Services\PosOrderService;
use Modules\POS\Application\Services\POSPostingService;
use Symfony\Component\HttpFoundation\Response;
final class POSApiController extends ApiController {
    public function __construct(
        private readonly PosTerminalService $posTerminalService,
        private readonly PosSessionService $posSessionService,
        private readonly PosOrderService $posOrderService,
        private readonly POSPostingService $posting,
    ) {}


    public function posTerminal(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->posTerminalService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->posTerminalService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->posTerminalService->list($companyId)]);
    }
    public function posSession(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->posSessionService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->posSessionService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->posSessionService->list($companyId)]);
    }
    public function posOrder(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->posOrderService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->posOrderService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->posOrderService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}