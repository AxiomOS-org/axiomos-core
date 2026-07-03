<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Http\Controllers\Api;
use Illuminate\Http\Request;
use Modules\FixedAssets\Application\Services\FixedAssetService;
use Modules\FixedAssets\Application\Services\DepreciationRunService;
use Modules\FixedAssets\Application\Services\FixedAssetsPostingService;
use Symfony\Component\HttpFoundation\Response;
final class FixedAssetsApiController extends ApiController {
    public function __construct(
        private readonly FixedAssetService $fixedAssetService,
        private readonly DepreciationRunService $depreciationRunService,
        private readonly FixedAssetsPostingService $posting,
    ) {}


    public function fixedAsset(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->fixedAssetService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->fixedAssetService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->fixedAssetService->list($companyId)]);
    }
    public function depreciationRun(Request $request): Response {
        if ($request->isMethod('post')) {
            return $this->ok(['data' => $this->depreciationRunService->create($request->all())], Response::HTTP_CREATED);
        }
        if ($request->isMethod('patch')) {
            return $this->ok(['data' => $this->depreciationRunService->update($request->all())]);
        }
        $companyId = $this->companyId($request);
        return $this->ok(['data' => $companyId === '' ? [] : $this->depreciationRunService->list($companyId)]);
    }
    public function postingSubmit(Request $request): Response {
        return $this->ok(['data' => $this->posting->submit($request->all())]);
    }

}