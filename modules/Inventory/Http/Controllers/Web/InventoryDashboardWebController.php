<?php
declare(strict_types=1);
namespace Modules\Inventory\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class InventoryDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Inventory Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Inventory', 'count' => 0, 'path' => '/inventory/dashboard'],
            ],
        ]);
    }
}