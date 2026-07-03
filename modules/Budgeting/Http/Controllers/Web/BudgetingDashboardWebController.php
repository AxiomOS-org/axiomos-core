<?php
declare(strict_types=1);
namespace Modules\Budgeting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Budgeting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class BudgetingDashboardWebController {
    public function index(Request $request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => 'Budgeting Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => 'Budgeting', 'count' => 0, 'path' => '/budgeting/dashboard'],
            ],
        ]);
    }
}