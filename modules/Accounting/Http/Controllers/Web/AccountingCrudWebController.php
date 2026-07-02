<?php
declare(strict_types=1);
namespace Modules\Accounting\Http\Controllers\Web;
use Illuminate\Http\Request;
use Modules\Accounting\Http\Support\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class AccountingCrudWebController {
    private const PAGES = ['accounts'=>['title'=>'Chart of Accounts','api'=>'/api/accounting/accounts','columns'=>['account_code','account_name','account_type'],'fields'=>['organization_id','company_id','branch_id','department_id','account_code','account_name','account_type']],'documents'=>['title'=>'Documents','api'=>'/api/accounting/documents','columns'=>['source_module','source_document_type','status'],'fields'=>['organization_id','company_id','source_module','source_document_type','source_document_id','document_date','currency','exchange_rate','status']],'journals'=>['title'=>'Journals','api'=>'/api/accounting/journals','columns'=>['journal_number','posting_date','debit_total','credit_total'],'fields'=>[]],'fiscal-years'=>['title'=>'Fiscal Years','api'=>'/api/accounting/fiscal-years','columns'=>['name','start_date','end_date','is_closed'],'fields'=>[]],'periods'=>['title'=>'Accounting Periods','api'=>'/api/accounting/periods','columns'=>['name','start_date','end_date','is_open'],'fields'=>[]],];
    public function index(Request $request, string $entity): Response { $page=self::PAGES[$entity]??null; if($page===null){ return new Response('Accounting admin page not found.', Response::HTTP_NOT_FOUND);} return BladeRenderer::render('crud.index',['title'=>$page['title'],'active'=>$entity,'entity'=>$entity,'entityLabel'=>$page['title'],'apiBase'=>$page['api'],'columns'=>$page['columns'],'fields'=>$page['fields']]); }
}

