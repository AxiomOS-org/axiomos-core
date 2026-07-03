<?php
declare(strict_types=1);
namespace Modules\Projects\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Projects\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class ProjectsDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Projects Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Projects', 'count' => 0, 'path' => '/projects/dashboard'],
            ],
        ]);
    }
}