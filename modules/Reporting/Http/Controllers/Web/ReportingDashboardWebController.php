<?php
declare(strict_types=1);
namespace Modules\Reporting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Reporting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ReportingDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Reporting Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Reporting', 'count' => 0, 'path' => '/reporting/dashboard'],
            ],
        ]);
    }
}