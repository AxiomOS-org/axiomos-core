<?php
declare(strict_types=1);
namespace Modules\Sales\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Sales\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class SalesDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Sales Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Sales', 'count' => 0, 'path' => '/sales/dashboard'],
            ],
        ]);
    }
}