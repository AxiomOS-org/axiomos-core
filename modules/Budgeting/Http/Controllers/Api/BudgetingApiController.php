<?php
declare(strict_types=1);
namespace Modules\Budgeting\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\Budgeting\Application\Services\BudgetVersionService;
use Modules\Budgeting\Application\Services\BudgetLineService;
use Symfony\Component\HttpFoundation\Response;
final class BudgetingApiController extends ApiController {
    public function __construct(
        private readonly BudgetVersionService $budgetVersionService,
        private readonly BudgetLineService $budgetLineService,
    ) {}


    public function budgetVersion(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->budgetVersionService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->budgetVersionService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->budgetVersionService->list($companyId)]);
    }
    public function budgetLine(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->budgetLineService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->budgetLineService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->budgetLineService->list($companyId)]);
    }
}