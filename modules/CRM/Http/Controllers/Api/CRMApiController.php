<?php
declare(strict_types=1);
namespace Modules\CRM\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\CRM\Application\Services\LeadService;
use Modules\CRM\Application\Services\OpportunityService;
use Modules\CRM\Application\Services\CrmActivityService;
use Symfony\Component\HttpFoundation\Response;
final class CRMApiController extends ApiController {
    public function __construct(
        private readonly LeadService $leadService,
        private readonly OpportunityService $opportunityService,
        private readonly CrmActivityService $crmActivityService,
    ) {}


    public function lead(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->leadService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->leadService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->leadService->list($companyId)]);
    }
    public function opportunity(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->opportunityService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->opportunityService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->opportunityService->list($companyId)]);
    }
    public function crmActivity(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->crmActivityService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->crmActivityService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->crmActivityService->list($companyId)]);
    }
}