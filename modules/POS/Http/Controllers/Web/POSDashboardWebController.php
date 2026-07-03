<?php
declare(strict_types=1);
namespace Modules\POS\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\POS\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class POSDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'POS Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'POS', 'count' => 0, 'path' => '/pos/dashboard'],
            ],
        ]);
    }
}