<?php
declare(strict_types=1);
namespace Modules\HR\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\HR\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class HRDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'HR Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'HR', 'count' => 0, 'path' => '/hr/dashboard'],
            ],
        ]);
    }
}