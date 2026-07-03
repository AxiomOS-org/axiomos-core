<?php
declare(strict_types=1);
namespace Modules\FixedAssets\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\FixedAssets\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class FixedAssetsDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'FixedAssets Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'FixedAssets', 'count' => 0, 'path' => '/assets/dashboard'],
            ],
        ]);
    }
}