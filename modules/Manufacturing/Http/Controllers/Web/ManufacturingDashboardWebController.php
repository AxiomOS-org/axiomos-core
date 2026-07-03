<?php
declare(strict_types=1);
namespace Modules\Manufacturing\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Manufacturing\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ManufacturingDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Manufacturing Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Manufacturing', 'count' => 0, 'path' => '/manufacturing/dashboard'],
            ],
        ]);
    }
}