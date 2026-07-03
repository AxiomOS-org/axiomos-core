<?php
declare(strict_types=1);
namespace Modules\Purchase\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Purchase\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class PurchaseDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Purchase Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Purchase', 'count' => 0, 'path' => '/purchase/dashboard'],
            ],
        ]);
    }
}