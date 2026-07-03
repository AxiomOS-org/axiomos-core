<?php
declare(strict_types=1);
namespace Modules\CRM\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\CRM\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class CRMDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'CRM Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'CRM', 'count' => 0, 'path' => '/crm/dashboard'],
            ],
        ]);
    }
}