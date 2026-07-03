<?php
declare(strict_types=1);
namespace Modules\Accounting\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use Modules\Accounting\Application\Services\AccountingPeriodService;
use Modules\Accounting\Application\Services\AuditTrailService;
use Modules\Accounting\Application\Services\AccountingPlatformHooks;
use Modules\Accounting\Application\Services\BalanceSheetService;
use Modules\Accounting\Application\Services\CashFlowService;
use Modules\Accounting\Application\Services\ChartOfAccountsService;
use Modules\Accounting\Application\Services\CostCenterService;
use Modules\Accounting\Application\Services\DocumentEngine;
use Modules\Accounting\Application\Services\DocumentService;
use Modules\Accounting\Application\Services\FiscalYearService;
use Modules\Accounting\Application\Services\JournalEngine;
use Modules\Accounting\Application\Services\JournalListService;
use Modules\Accounting\Application\Services\LedgerEngine;
use Modules\Accounting\Application\Services\MultiCurrencyService;
use Modules\Accounting\Application\Services\PostingEngine;
use Modules\Accounting\Application\Services\ProfitAndLossService;
use Modules\Accounting\Application\Services\ReversalEngine;
use Modules\Accounting\Application\Services\TrialBalanceService;
use Modules\Accounting\Application\Services\VoucherEngine;
use Modules\Accounting\Database\Seeders\AccountingDemoSeeder;
use Modules\Accounting\Domain\Repositories\Contracts\AccountRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\DimensionRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\DocumentRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\ExchangeRateRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\FiscalYearRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\JournalLineRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\JournalRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\LedgerBalanceRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\PeriodRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\PostingLogRepositoryInterface;
use Modules\Accounting\Domain\Repositories\Contracts\VoucherTypeRepositoryInterface;
use Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface;
use Modules\Accounting\Http\Controllers\Api\AccountingApiController;
use Modules\Accounting\Http\Controllers\Web\AccountingCrudWebController;
use Modules\Accounting\Http\Controllers\Web\AccountingDashboardWebController;
use Modules\Accounting\Infrastructure\Persistence\EloquentAccountRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentDimensionRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentDocumentRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentExchangeRateRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentFiscalYearRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentJournalLineRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentJournalRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentLedgerBalanceRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentPeriodRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentPostingLogRepository;
use Modules\Accounting\Infrastructure\Persistence\EloquentVoucherTypeRepository;
use Modules\Accounting\Policies\AccountPolicy;
use Modules\Accounting\Policies\DocumentPolicy;
use Modules\Accounting\Policies\FiscalYearPolicy;
use Modules\Accounting\Policies\JournalPolicy;
use Modules\Accounting\Policies\PeriodPolicy;
use Modules\Accounting\Policies\VoucherTypePolicy;
final class AccountingServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface $container): void {
        $container->instance('module.accounting', new ModuleInfo('Accounting', '1.0.0'));
        $container->singleton(AccountRepositoryInterface::class, EloquentAccountRepository::class);
        $container->singleton(DocumentRepositoryInterface::class, EloquentDocumentRepository::class);
        $container->singleton(JournalRepositoryInterface::class, EloquentJournalRepository::class);
        $container->singleton(JournalLineRepositoryInterface::class, EloquentJournalLineRepository::class);
        $container->singleton(LedgerBalanceRepositoryInterface::class, EloquentLedgerBalanceRepository::class);
        $container->singleton(PostingLogRepositoryInterface::class, EloquentPostingLogRepository::class);
        $container->singleton(FiscalYearRepositoryInterface::class, EloquentFiscalYearRepository::class);
        $container->singleton(PeriodRepositoryInterface::class, EloquentPeriodRepository::class);
        $container->singleton(VoucherTypeRepositoryInterface::class, EloquentVoucherTypeRepository::class);
        $container->singleton(DimensionRepositoryInterface::class, EloquentDimensionRepository::class);
        $container->singleton(ExchangeRateRepositoryInterface::class, EloquentExchangeRateRepository::class);
        $container->singleton(AccountingPlatformHooks::class, AccountingPlatformHooks::class);
        $container->singleton(JournalEngine::class, JournalEngine::class);
        $container->singleton(LedgerEngine::class, LedgerEngine::class);
        $container->singleton(DocumentEngine::class, DocumentEngine::class);
        $container->singleton(PostingEngine::class, PostingEngine::class);
        $container->singleton(PostingEngineInterface::class, PostingEngine::class);
        $container->singleton(ReversalEngine::class, ReversalEngine::class);
        $container->singleton(VoucherEngine::class, VoucherEngine::class);
        $container->singleton(FiscalYearService::class, FiscalYearService::class);
        $container->singleton(AccountingPeriodService::class, AccountingPeriodService::class);
        $container->singleton(ChartOfAccountsService::class, ChartOfAccountsService::class);
        $container->singleton(TrialBalanceService::class, TrialBalanceService::class);
        $container->singleton(BalanceSheetService::class, BalanceSheetService::class);
        $container->singleton(ProfitAndLossService::class, ProfitAndLossService::class);
        $container->singleton(CashFlowService::class, CashFlowService::class);
        $container->singleton(MultiCurrencyService::class, MultiCurrencyService::class);
        $container->singleton(CostCenterService::class, CostCenterService::class);
        $container->singleton(DocumentService::class, DocumentService::class);
        $container->singleton(AuditTrailService::class, AuditTrailService::class);
        $container->singleton(JournalListService::class, JournalListService::class);
        $container->singleton(AccountPolicy::class, AccountPolicy::class);
        $container->singleton(FiscalYearPolicy::class, FiscalYearPolicy::class);
        $container->singleton(PeriodPolicy::class, PeriodPolicy::class);
        $container->singleton(VoucherTypePolicy::class, VoucherTypePolicy::class);
        $container->singleton(DocumentPolicy::class, DocumentPolicy::class);
        $container->singleton(JournalPolicy::class, JournalPolicy::class);
        $container->singleton(AccountingApiController::class, AccountingApiController::class);
        $container->singleton(AccountingDashboardWebController::class, AccountingDashboardWebController::class);
        $container->singleton(AccountingCrudWebController::class, AccountingCrudWebController::class);
    }
    public function boot(ContainerInterface $container): void {
        $migrations = dirname(__DIR__).DIRECTORY_SEPARATOR.'Database'.DIRECTORY_SEPARATOR.'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([$migrations]);
        if ((getenv('APP_ENV') ?: 'production') !== 'production') {
            (new AccountingDemoSeeder($container->make(PostingEngineInterface::class)))->run();
        }
        if (!$container->has(Router::class)) { return; }
        $router = $container->make(Router::class); $registrar = require dirname(__DIR__).DIRECTORY_SEPARATOR.'routes.php'; if (is_callable($registrar)) { $registrar($router, $container); }
    }
}

