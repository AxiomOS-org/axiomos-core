<?php
declare(strict_types=1);
namespace Modules\Accounting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Accounting\Domain\Models\Account;
use Modules\Accounting\Domain\Models\Document;
use Modules\Accounting\Domain\Models\Journal;
use Modules\Accounting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class AccountingDashboardWebController {
    public function index(Request $request): Response { return BladeRenderer::render('dashboard.index', ['title'=>'Accounting Dashboard','active'=>'dashboard','cards'=>[['label'=>'Accounts','count'=>Account::query()->count(),'path'=>'/accounting/accounts'],['label'=>'Documents','count'=>Document::query()->count(),'path'=>'/accounting/documents'],['label'=>'Journals','count'=>Journal::query()->count(),'path'=>'/accounting/journals']]]); }
}

