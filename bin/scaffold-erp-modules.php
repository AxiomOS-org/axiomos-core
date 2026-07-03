<?php

declare(strict_types=1);

/**
 * Scaffolds Run 2–7 ERP business modules (Sales through Reporting).
 * Usage: php bin/scaffold-erp-modules.php
 */

$basePath = dirname(__DIR__);
$modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';

$definitions = [
    'Sales' => [
        'priority' => 200,
        'description' => 'Sales — customers, orders, invoices, posting via Accounting engine',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'sales',
        'apiPrefix' => 'sales',
        'entities' => [
            ['Customer', 'sales_customers', ['organization_id', 'company_id', 'name', 'email', 'phone', 'status']],
            ['SalesOrder', 'sales_orders', ['organization_id', 'company_id', 'customer_id', 'order_number', 'status', 'total_amount', 'currency']],
            ['SalesInvoice', 'sales_invoices', ['organization_id', 'company_id', 'customer_id', 'invoice_number', 'status', 'total_amount', 'currency', 'journal_id'], true],
        ],
    ],
    'Purchase' => [
        'priority' => 210,
        'description' => 'Purchase — vendors, POs, GRN, bills, posting via Accounting engine',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'purchase',
        'apiPrefix' => 'purchase',
        'entities' => [
            ['Vendor', 'purchase_vendors', ['organization_id', 'company_id', 'name', 'email', 'phone', 'status']],
            ['PurchaseOrder', 'purchase_orders', ['organization_id', 'company_id', 'vendor_id', 'order_number', 'status', 'total_amount', 'currency']],
            ['PurchaseBill', 'purchase_bills', ['organization_id', 'company_id', 'vendor_id', 'bill_number', 'status', 'total_amount', 'currency', 'journal_id'], true],
        ],
    ],
    'Inventory' => [
        'priority' => 220,
        'description' => 'Inventory — warehouses, items, stock movements and balances',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'inventory',
        'apiPrefix' => 'inventory',
        'entities' => [
            ['Warehouse', 'inventory_warehouses', ['organization_id', 'company_id', 'code', 'name', 'status']],
            ['Item', 'inventory_items', ['organization_id', 'company_id', 'sku', 'name', 'unit', 'status']],
            ['StockMovement', 'inventory_stock_movements', ['organization_id', 'company_id', 'warehouse_id', 'item_id', 'movement_type', 'quantity', 'reference']],
        ],
    ],
    'HR' => [
        'priority' => 230,
        'description' => 'HR & Payroll — employees, attendance, leave, payroll runs',
        'dependencies' => ['Core', 'Organization', 'Accounting', 'Identity'],
        'urlPrefix' => 'hr',
        'apiPrefix' => 'hr',
        'entities' => [
            ['Employee', 'hr_employees', ['organization_id', 'company_id', 'employee_code', 'full_name', 'email', 'status']],
            ['AttendanceRecord', 'hr_attendance_records', ['organization_id', 'company_id', 'employee_id', 'work_date', 'status', 'hours_worked']],
            ['PayrollRun', 'hr_payroll_runs', ['organization_id', 'company_id', 'period_label', 'status', 'total_amount', 'currency', 'journal_id'], true],
        ],
    ],
    'CRM' => [
        'priority' => 240,
        'description' => 'CRM — leads, opportunities, activities',
        'dependencies' => ['Core', 'Organization', 'Sales'],
        'urlPrefix' => 'crm',
        'apiPrefix' => 'crm',
        'entities' => [
            ['Lead', 'crm_leads', ['organization_id', 'company_id', 'name', 'email', 'source', 'status']],
            ['Opportunity', 'crm_opportunities', ['organization_id', 'company_id', 'lead_id', 'title', 'stage', 'amount', 'currency']],
            ['CrmActivity', 'crm_activities', ['organization_id', 'company_id', 'subject', 'activity_type', 'status', 'due_at']],
        ],
    ],
    'Projects' => [
        'priority' => 250,
        'description' => 'Projects — projects, tasks, timesheets',
        'dependencies' => ['Core', 'Organization', 'HR'],
        'urlPrefix' => 'projects',
        'apiPrefix' => 'projects',
        'entities' => [
            ['Project', 'projects_projects', ['organization_id', 'company_id', 'code', 'name', 'status', 'budget_amount']],
            ['ProjectTask', 'projects_tasks', ['organization_id', 'company_id', 'project_id', 'title', 'status', 'assignee_id']],
            ['Timesheet', 'projects_timesheets', ['organization_id', 'company_id', 'project_id', 'employee_id', 'work_date', 'hours', 'status']],
        ],
    ],
    'Manufacturing' => [
        'priority' => 260,
        'description' => 'Manufacturing — BOM, work orders, production runs',
        'dependencies' => ['Core', 'Organization', 'Inventory', 'Accounting'],
        'urlPrefix' => 'manufacturing',
        'apiPrefix' => 'manufacturing',
        'entities' => [
            ['BillOfMaterial', 'manufacturing_boms', ['organization_id', 'company_id', 'item_id', 'version', 'status']],
            ['WorkOrder', 'manufacturing_work_orders', ['organization_id', 'company_id', 'bom_id', 'order_number', 'status', 'quantity']],
            ['ProductionRun', 'manufacturing_production_runs', ['organization_id', 'company_id', 'work_order_id', 'status', 'quantity_produced', 'journal_id'], true],
        ],
    ],
    'POS' => [
        'priority' => 270,
        'description' => 'POS — terminals, sessions, orders',
        'dependencies' => ['Core', 'Organization', 'Sales', 'Inventory', 'Accounting'],
        'urlPrefix' => 'pos',
        'apiPrefix' => 'pos',
        'entities' => [
            ['PosTerminal', 'pos_terminals', ['organization_id', 'company_id', 'code', 'name', 'status']],
            ['PosSession', 'pos_sessions', ['organization_id', 'company_id', 'terminal_id', 'opened_at', 'closed_at', 'status']],
            ['PosOrder', 'pos_orders', ['organization_id', 'company_id', 'session_id', 'order_number', 'status', 'total_amount', 'currency', 'journal_id'], true],
        ],
    ],
    'FixedAssets' => [
        'priority' => 280,
        'description' => 'Fixed Assets — asset register and depreciation',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'assets',
        'apiPrefix' => 'assets',
        'entities' => [
            ['FixedAsset', 'fixed_assets', ['organization_id', 'company_id', 'asset_code', 'name', 'status', 'acquisition_cost', 'currency']],
            ['DepreciationRun', 'fixed_asset_depreciation_runs', ['organization_id', 'company_id', 'period_label', 'status', 'total_amount', 'currency', 'journal_id'], true],
        ],
    ],
    'Budgeting' => [
        'priority' => 290,
        'description' => 'Budgeting — budget versions and lines',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'budgeting',
        'apiPrefix' => 'budgeting',
        'entities' => [
            ['BudgetVersion', 'budget_versions', ['organization_id', 'company_id', 'name', 'fiscal_year', 'status']],
            ['BudgetLine', 'budget_lines', ['organization_id', 'company_id', 'budget_version_id', 'account_id', 'period_label', 'amount', 'currency']],
        ],
    ],
    'Reporting' => [
        'priority' => 300,
        'description' => 'Reporting & BI — read-only financial dashboards',
        'dependencies' => ['Core', 'Organization', 'Accounting'],
        'urlPrefix' => 'reporting',
        'apiPrefix' => 'reporting',
        'entities' => [
            ['ReportDefinition', 'reporting_definitions', ['organization_id', 'company_id', 'code', 'name', 'report_type', 'status']],
            ['ReportSnapshot', 'reporting_snapshots', ['organization_id', 'company_id', 'report_definition_id', 'snapshot_date', 'status', 'payload_json']],
        ],
        'readOnlyReports' => true,
    ],
];

foreach ($definitions as $moduleName => $def) {
    $moduleDir = $modulesPath . DIRECTORY_SEPARATOR . $moduleName;
    if (is_dir($moduleDir)) {
        echo "Skip existing: {$moduleName}\n";
        continue;
    }

    echo "Scaffolding: {$moduleName}\n";
    scaffoldModule($moduleDir, $moduleName, $def);
}

updateRouteCatalog($basePath);
echo "Done.\n";

function scaffoldModule(string $dir, string $moduleName, array $def): void
{
    $ns = "Modules\\{$moduleName}";
    $urlPrefix = $def['urlPrefix'];
    $apiPrefix = $def['apiPrefix'];
    $entities = $def['entities'];
    $readOnlyReports = $def['readOnlyReports'] ?? false;

    mkdir($dir . '/Providers', 0777, true);
    mkdir($dir . '/Database/Migrations', 0777, true);
    mkdir($dir . '/Domain/Models', 0777, true);
    mkdir($dir . '/Domain/Repositories/Contracts', 0777, true);
    mkdir($dir . '/Infrastructure/Persistence', 0777, true);
    mkdir($dir . '/Application/Services', 0777, true);
    mkdir($dir . '/Http/Controllers/Api', 0777, true);
    mkdir($dir . '/Http/Controllers/Web', 0777, true);
    mkdir($dir . '/Http/Support', 0777, true);
    mkdir($dir . '/Policies', 0777, true);
    mkdir($dir . '/Resources/views/layouts', 0777, true);
    mkdir($dir . '/Resources/views/dashboard', 0777, true);
    mkdir($dir . '/Resources/views/crud', 0777, true);

    $deps = json_encode($def['dependencies'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($dir . '/module.json', <<<JSON
{
    "name": "{$moduleName}",
    "version": "1.0.0",
    "description": "{$def['description']}",
    "provider": "{$ns}\\\\Providers\\\\{$moduleName}ServiceProvider",
    "enabled": true,
    "priority": {$def['priority']},
    "dependencies": {$deps},
    "authors": [{"name": "AxiomOS Team"}],
    "minimumCoreVersion": "1.0.0"
}
JSON);

  $migrationTables = '';
    foreach ($entities as $entity) {
        [$className, $table, $columns] = $entity;
        $migrationTables .= migrationTableBlock($table, $columns);
    }

    $migrationFile = $dir . '/Database/Migrations/2026_07_03_' . $def['priority'] . '000_create_' . strtolower($moduleName) . '_tables.php';
    file_put_contents($migrationFile, <<<PHP
<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
{$migrationTables}
    }
    public function down(): void {
PHP);
    $down = '';
    foreach (array_reverse($entities) as $entity) {
        $down .= "        Schema::dropIfExists('{$entity[1]}');\n";
    }
    file_put_contents($migrationFile, file_get_contents($migrationFile) . $down . "    }\n};\n", FILE_APPEND);

    foreach ($entities as $entity) {
        [$className, $table, $columns] = $entity;
        $fillable = var_export($columns, true);
        file_put_contents($dir . "/Domain/Models/{$className}.php", <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Domain\\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class {$className} extends Model {
    use HasUuids;
    protected \$table = '{$table}';
    public \$incrementing = false;
    protected \$keyType = 'string';
    protected \$fillable = {$fillable};
}
PHP);

        file_put_contents($dir . "/Domain/Repositories/Contracts/{$className}RepositoryInterface.php", <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Domain\\Repositories\\Contracts;
use {$ns}\\Domain\\Models\\{$className};
interface {$className}RepositoryInterface {
    /** @return list<array<string,mixed>> */
    public function listByCompany(string \$companyId): array;
    public function find(string \$id): ?{$className};
    public function create(array \$attributes): {$className};
    public function update({$className} \$model, array \$attributes): {$className};
}
PHP);

        file_put_contents($dir . "/Infrastructure/Persistence/Eloquent{$className}Repository.php", <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Infrastructure\\Persistence;
use {$ns}\\Domain\\Models\\{$className};
use {$ns}\\Domain\\Repositories\\Contracts\\{$className}RepositoryInterface;
final class Eloquent{$className}Repository implements {$className}RepositoryInterface {
    public function listByCompany(string \$companyId): array {
        return {$className}::query()->where('company_id', \$companyId)->orderBy('created_at', 'desc')->get()->map(static fn ({$className} \$m): array => \$m->toArray())->all();
    }
    public function find(string \$id): ?{$className} { return {$className}::query()->find(\$id); }
    public function create(array \$attributes): {$className} { return {$className}::query()->create(\$attributes); }
    public function update({$className} \$model, array \$attributes): {$className} { \$model->fill(\$attributes); \$model->save(); return \$model; }
}
PHP);

        file_put_contents($dir . "/Application/Services/{$className}Service.php", <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Application\\Services;
use {$ns}\\Domain\\Repositories\\Contracts\\{$className}RepositoryInterface;
final class {$className}Service {
    public function __construct(private readonly {$className}RepositoryInterface \$repository) {}
    public function list(string \$companyId): array { return \$this->repository->listByCompany(\$companyId); }
    public function create(array \$payload): array { return \$this->repository->create(\$payload)->toArray(); }
}
PHP);
    }

    $hasPosting = array_filter($entities, static fn ($e) => ($e[3] ?? false) === true);
    if ($hasPosting !== []) {
        $postingEntity = array_values($hasPosting)[0][0];
        file_put_contents($dir . '/Application/Services/' . $moduleName . 'PostingService.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Application\\Services;
use Modules\Accounting\Application\DTOs\PostingRequest;
use Modules\Accounting\Domain\Services\Contracts\PostingEngineInterface;
use {$ns}\\Domain\\Repositories\\Contracts\\{$postingEntity}RepositoryInterface;
final class {$moduleName}PostingService {
    public function __construct(private readonly PostingEngineInterface \$posting, private readonly {$postingEntity}RepositoryInterface \$documents) {}
    public function submit(array \$payload): array {
        \$lines = (array) (\$payload['lines'] ?? []);
        if (\$lines === []) {
            return ['success' => false, 'errors' => ['Posting lines are required.']];
        }
        \$result = \$this->posting->submit(new PostingRequest(
            (string) (\$payload['idempotency_key'] ?? uniqid('{$apiPrefix}:', true)),
            '{$moduleName}',
            (string) (\$payload['document_type'] ?? '{$apiPrefix}_document'),
            (string) (\$payload['document_id'] ?? ''),
            (string) (\$payload['company_id'] ?? ''),
            isset(\$payload['organization_id']) ? (string) \$payload['organization_id'] : null,
            isset(\$payload['branch_id']) ? (string) \$payload['branch_id'] : null,
            isset(\$payload['department_id']) ? (string) \$payload['department_id'] : null,
            (string) (\$payload['posting_date'] ?? date('Y-m-d')),
            strtoupper((string) (\$payload['currency'] ?? 'USD')),
            (string) (\$payload['exchange_rate'] ?? '1'),
            (string) (\$payload['voucher_type'] ?? 'JV'),
            \$lines,
        ));
        if (\$result->success && isset(\$payload['id'])) {
            \$doc = \$this->documents->find((string) \$payload['id']);
            if (\$doc !== null) {
                \$this->documents->update(\$doc, ['status' => 'posted', 'journal_id' => \$result->journalId]);
            }
        }
        return \$result->toArray();
    }
}
PHP);
    }

    file_put_contents($dir . '/Http/Controllers/Api/ApiController.php', <<<'PHP'
<?php
declare(strict_types=1);
namespace MODULE_NS\Http\Controllers\Api;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
abstract class ApiController {
    protected function ok(array $payload = [], int $status = Response::HTTP_OK): JsonResponse {
        return new JsonResponse($payload, $status);
    }
    protected function companyId(\Illuminate\Http\Request $request): string {
        return (string) ($request->query('company_id', $request->input('company_id', '')));
    }
}
PHP);
    replaceInFile($dir . '/Http/Controllers/Api/ApiController.php', 'MODULE_NS', $ns);

    $apiMethods = '';
    $bindings = '';
    $policyBindings = '';
    $routeApi = '';
    $crudPages = [];
    foreach ($entities as $entity) {
        [$className, $table, $columns] = $entity;
        $slug = kebabCase($className);
        $apiMethods .= apiMethod($className, $slug);
        $bindings .= "        \$container->singleton(\\{$ns}\\Domain\\Repositories\\Contracts\\{$className}RepositoryInterface::class, \\{$ns}\\Infrastructure\\Persistence\\Eloquent{$className}Repository::class);\n";
        $bindings .= "        \$container->singleton(\\{$ns}\\Application\\Services\\{$className}Service::class, \\{$ns}\\Application\\Services\\{$className}Service::class);\n";
        $policyBindings .= "        \$container->singleton(\\{$ns}\\Policies\\{$className}Policy::class, \\{$ns}\\Policies\\{$className}Policy::class);\n";
        file_put_contents($dir . "/Policies/{$className}Policy.php", <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Policies;
final class {$className}Policy {
    public function viewAny(): bool { return true; }
    public function create(): bool { return true; }
}
PHP);
        $routeApi .= "    \$router->get('/api/{$apiPrefix}/{$slug}', static fn(Request \$request) => \$api->{$slug}(\$request));\n";
        $routeApi .= "    \$router->post('/api/{$apiPrefix}/{$slug}', static fn(Request \$request) => \$api->{$slug}(\$request));\n";
        $crudPages[$slug] = ['title' => humanize($className), 'api' => "/api/{$apiPrefix}/{$slug}", 'columns' => array_slice($columns, -4)];
    }

    if ($hasPosting !== []) {
        $routeApi .= "    \$router->post('/api/{$apiPrefix}/posting/submit', static fn(Request \$request) => \$api->postingSubmit(\$request));\n";
        $apiMethods .= "\n    public function postingSubmit(Request \$request): Response {\n        return \$this->ok(['data' => \$this->posting->submit(\$request->all())]);\n    }\n";
        $bindings .= "        \$container->singleton(\\{$ns}\\Application\\Services\\{$moduleName}PostingService::class, \\{$ns}\\Application\\Services\\{$moduleName}PostingService::class);\n";
    }

    if ($readOnlyReports) {
        $routeApi .= "    \$router->get('/api/{$apiPrefix}/dashboard', static fn(Request \$request) => \$api->dashboard(\$request));\n";
        $apiMethods .= "\n    public function dashboard(Request \$request): Response {\n        \$companyId = \$this->companyId(\$request);\n        return \$this->ok(['data' => ['company_id' => \$companyId, 'reports' => \$this->reporting->dashboard(\$companyId)]]);\n    }\n";
        $bindings .= "        \$container->singleton(\\{$ns}\\Application\\Services\\ReportingDashboardService::class, \\{$ns}\\Application\\Services\\ReportingDashboardService::class);\n";
        file_put_contents($dir . '/Application/Services/ReportingDashboardService.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Application\\Services;
use Modules\Accounting\Application\Services\TrialBalanceService;
use Modules\Accounting\Application\Services\ProfitAndLossService;
use Modules\Accounting\Application\Services\BalanceSheetService;
final class ReportingDashboardService {
    public function __construct(private readonly TrialBalanceService \$trialBalance, private readonly ProfitAndLossService \$pnl, private readonly BalanceSheetService \$balanceSheet) {}
    public function dashboard(string \$companyId): array {
        if (\$companyId === '') { return []; }
        return ['trial_balance' => \$this->trialBalance->generate(\$companyId), 'profit_loss' => \$this->pnl->generate(\$companyId), 'balance_sheet' => \$this->balanceSheet->generate(\$companyId)];
    }
}
PHP);
    }

    $constructorInject = '';
    $props = '';
    foreach ($entities as $entity) {
        $className = $entity[0];
        $var = lcfirst($className) . 'Service';
        $props .= "        private readonly {$className}Service \${$var},\n";
        $constructorInject .= "        {$className}Service \${$var},\n";
    }
    if ($hasPosting !== []) {
        $props .= "        private readonly {$moduleName}PostingService \$posting,\n";
        $constructorInject .= "        {$moduleName}PostingService \$posting,\n";
    }
    if ($readOnlyReports) {
        $props .= "        private readonly ReportingDashboardService \$reporting,\n";
        $constructorInject .= "        ReportingDashboardService \$reporting,\n";
    }

    file_put_contents($dir . '/Http/Controllers/Api/' . $moduleName . 'ApiController.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Http\\Controllers\\Api;
use Illuminate\Http\Request;
use {$ns}\\Application\\Services\\*;
use Symfony\Component\HttpFoundation\Response;
final class {$moduleName}ApiController extends ApiController {
    public function __construct(
{$constructorInject}    ) {}
{$apiMethods}
}
PHP);

    replaceInFile($dir . '/Http/Controllers/Api/' . $moduleName . 'ApiController.php', 'use ' . $ns . '\\Application\\Services\\*;', buildUseStatements($entities, $hasPosting, $readOnlyReports, $moduleName, $ns));

    $crudConst = exportCrudPages($crudPages);
    copyAccountingViews($dir, $moduleName, $urlPrefix);

    file_put_contents($dir . '/Http/Support/BladeRenderer.php', str_replace('Accounting', $moduleName, file_get_contents(dirname(__DIR__) . '/modules/Accounting/Http/Support/BladeRenderer.php')));

    file_put_contents($dir . '/Http/Controllers/Web/' . $moduleName . 'DashboardWebController.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Http\\Controllers\\Web;
use Illuminate\Http\Request;
use {$ns}\\Http\\Support\\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class {$moduleName}DashboardWebController {
    public function index(Request \$request): Response {
        return BladeRenderer::render('dashboard.index', [
            'title' => '{$moduleName} Dashboard',
            'active' => 'dashboard',
            'cards' => [
                ['label' => '{$moduleName}', 'count' => 0, 'path' => '/{$urlPrefix}/dashboard'],
            ],
        ]);
    }
}
PHP);

    file_put_contents($dir . '/Http/Controllers/Web/' . $moduleName . 'CrudWebController.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Http\\Controllers\\Web;
use Illuminate\Http\Request;
use {$ns}\\Http\\Support\\BladeRenderer;
use Symfony\Component\HttpFoundation\Response;
final class {$moduleName}CrudWebController {
    private const PAGES = {$crudConst};
    public function index(Request \$request, string \$entity): Response {
        \$page = self::PAGES[\$entity] ?? null;
        if (\$page === null) {
            return new Response('{$moduleName} admin page not found.', Response::HTTP_NOT_FOUND);
        }
        return BladeRenderer::render('crud.index', [
            'title' => \$page['title'],
            'active' => \$entity,
            'entity' => \$entity,
            'entityLabel' => \$page['title'],
            'apiBase' => \$page['api'],
            'columns' => \$page['columns'],
            'fields' => \$page['columns'],
        ]);
    }
}
PHP);

    $webRoutes = '';
    foreach (array_keys($crudPages) as $slug) {
        $webRoutes .= "    \$router->get('/{$urlPrefix}/{$slug}', static fn(Request \$request) => \$crud->index(\$request, '{$slug}'));\n";
    }

    file_put_contents($dir . '/routes.php', <<<PHP
<?php
declare(strict_types=1);
use App\Core\Container\Contracts\ContainerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use {$ns}\\Http\\Controllers\\Api\\{$moduleName}ApiController;
use {$ns}\\Http\\Controllers\\Web\\{$moduleName}CrudWebController;
use {$ns}\\Http\\Controllers\\Web\\{$moduleName}DashboardWebController;
return static function (Router \$router, ContainerInterface \$container): void {
    \$api = \$container->make({$moduleName}ApiController::class);
{$routeApi}
    \$dashboard = \$container->make({$moduleName}DashboardWebController::class);
    \$crud = \$container->make({$moduleName}CrudWebController::class);
    \$router->get('/{$urlPrefix}', static fn(Request \$request) => \$dashboard->index(\$request));
    \$router->get('/{$urlPrefix}/dashboard', static fn(Request \$request) => \$dashboard->index(\$request));
{$webRoutes}};
PHP);

    file_put_contents($dir . '/Providers/' . $moduleName . 'ServiceProvider.php', <<<PHP
<?php
declare(strict_types=1);
namespace {$ns}\\Providers;
use App\Core\Container\Contracts\ContainerInterface;
use App\Core\Module\Support\ModuleInfo;
use App\Core\Module\Support\ModuleServiceProvider;
use App\Infrastructure\Database\DatabaseBootstrap;
use App\Infrastructure\Database\MigrationRunner;
use Illuminate\Routing\Router;
use {$ns}\\Http\\Controllers\\Api\\{$moduleName}ApiController;
use {$ns}\\Http\\Controllers\\Web\\{$moduleName}CrudWebController;
use {$ns}\\Http\\Controllers\\Web\\{$moduleName}DashboardWebController;
final class {$moduleName}ServiceProvider extends ModuleServiceProvider {
    public function register(ContainerInterface \$container): void {
        \$container->instance('module.' . strtolower('{$moduleName}'), new ModuleInfo('{$moduleName}', '1.0.0'));
{$bindings}{$policyBindings}
        \$container->singleton({$moduleName}ApiController::class, {$moduleName}ApiController::class);
        \$container->singleton({$moduleName}DashboardWebController::class, {$moduleName}DashboardWebController::class);
        \$container->singleton({$moduleName}CrudWebController::class, {$moduleName}CrudWebController::class);
    }
    public function boot(ContainerInterface \$container): void {
        \$migrations = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        MigrationRunner::create(DatabaseBootstrap::capsule())->runIfNeeded([\$migrations]);
        if (! \$container->has(Router::class)) { return; }
        \$router = \$container->make(Router::class);
        \$registrar = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes.php';
        if (is_callable(\$registrar)) { \$registrar(\$router, \$container); }
    }
}
PHP);

    scaffoldFeatureTest(dirname(__DIR__), $moduleName, $apiPrefix, array_keys($crudPages));
}

function migrationTableBlock(string $table, array $columns): string
{
    $lines = "        Schema::create('{$table}', static function (Blueprint \$table): void {\n";
    $lines .= "            \$table->uuid('id')->primary();\n";
    foreach ($columns as $col) {
        if (str_ends_with($col, '_id')) {
            $lines .= "            \$table->uuid('{$col}')->nullable();\n";
        } elseif (in_array($col, ['total_amount', 'amount', 'acquisition_cost', 'budget_amount', 'hours_worked', 'hours', 'quantity', 'quantity_produced'], true)) {
            $lines .= "            \$table->decimal('{$col}', 18, 6)->default(0);\n";
        } elseif ($col === 'payload_json') {
            $lines .= "            \$table->jsonb('{$col}')->nullable();\n";
        } elseif (in_array($col, ['due_at', 'opened_at', 'closed_at', 'work_date', 'snapshot_date'], true)) {
            $lines .= "            \$table->timestamp('{$col}')->nullable();\n";
        } else {
            $lines .= "            \$table->string('{$col}')->nullable();\n";
        }
    }
    $lines .= "            \$table->timestamps();\n            \$table->softDeletes();\n        });\n";

    return $lines;
}

function apiMethod(string $className, string $slug): string
{
    $service = lcfirst($className) . 'Service';

    return <<<PHP

    public function {$slug}(Request \$request): Response {
        if (\$request->isMethod('post')) {
            return \$this->ok(['data' => \$this->{$service}->create(\$request->all())], Response::HTTP_CREATED);
        }
        \$companyId = \$this->companyId(\$request);
        return \$this->ok(['data' => \$companyId === '' ? [] : \$this->{$service}->list(\$companyId)]);
    }
PHP;
}

function kebabCase(string $value): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $value) ?? $value);
}

function humanize(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', ucwords(preg_replace('/(?<!^)[A-Z]/', ' $0', $value)) ?? $value));
}

function exportCrudPages(array $pages): string
{
    $out = "[\n";
    foreach ($pages as $slug => $page) {
        $out .= "        '{$slug}' => ['title' => '{$page['title']}', 'api' => '{$page['api']}', 'columns' => " . var_export($page['columns'], true) . "],\n";
    }

    return $out . '    ]';
}

function buildUseStatements(array $entities, array $hasPosting, bool $readOnly, string $moduleName, string $ns): string
{
    $uses = '';
    foreach ($entities as $entity) {
        $uses .= "use {$ns}\\Application\\Services\\{$entity[0]}Service;\n";
    }
    if ($hasPosting !== []) {
        $uses .= "use {$ns}\\Application\\Services\\{$moduleName}PostingService;\n";
    }
    if ($readOnly) {
        $uses .= "use {$ns}\\Application\\Services\\ReportingDashboardService;\n";
    }

    return rtrim($uses);
}

function replaceInFile(string $path, string $search, string $replace): void
{
    file_put_contents($path, str_replace($search, $replace, (string) file_get_contents($path)));
}

function copyAccountingViews(string $dir, string $moduleName, string $urlPrefix): void
{
    $accountingViews = dirname(__DIR__) . '/modules/Accounting/Resources/views';
    foreach (['layouts/admin.blade.php', 'dashboard/index.blade.php', 'crud/index.blade.php'] as $view) {
        $content = (string) file_get_contents($accountingViews . '/' . $view);
        $content = str_replace(['Accounting', 'accounting', 'AxiomOS ERP'], [$moduleName, $urlPrefix, 'AxiomOS ERP'], $content);
        file_put_contents($dir . '/Resources/views/' . $view, $content);
    }
}

function scaffoldFeatureTest(string $basePath, string $moduleName, string $apiPrefix, array $slugs): void
{
    $testDir = $basePath . '/tests/Feature/Modules/' . $moduleName;
    mkdir($testDir, 0777, true);
    $routeChecks = '';
    foreach ($slugs as $slug) {
        $routeChecks .= "            '/api/{$apiPrefix}/{$slug}?company_id=' . \$companyId,\n";
    }
    file_put_contents($testDir . '/' . $moduleName . 'PlatformTest.php', <<<PHP
<?php
declare(strict_types=1);
namespace Tests\Feature\Modules\\{$moduleName};
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Stability\KernelTestHarness;
final class {$moduleName}PlatformTest extends KernelTestHarness {
    public function test_module_routes_do_not_return_500(): void {
        \$company = \$this->decodeJson((string) \$this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'))->getContent());
        \$companyId = (string) (\$company['data'][0]['id'] ?? '');
        \$failures = [];
        foreach ([
{$routeChecks}            '/{$apiPrefix}',
            '/{$apiPrefix}/dashboard',
        ] as \$path) {
            \$response = \$this->kernel->handle(Request::create(\$path, 'GET'));
            if (\$response->getStatusCode() >= 500) {
                \$failures[] = \$path . ' => ' . \$response->getStatusCode();
            }
        }
        self::assertSame([], \$failures, implode("\\n", \$failures));
    }
}
PHP);
}

function updateRouteCatalog(string $basePath): void
{
    $path = $basePath . '/tests/Support/Stability/RouteCatalog.php';
    $content = (string) file_get_contents($path);
    $modules = ['sales', 'purchase', 'inventory', 'hr', 'crm', 'projects', 'manufacturing', 'pos', 'assets', 'budgeting', 'reporting'];
    $insert = '';
    foreach ($modules as $m) {
        $insert .= "            '/{$m}',\n            '/{$m}/dashboard',\n";
    }
    if (! str_contains($content, "'/sales',")) {
        $content = str_replace("            '/accounting/periods',\n", "            '/accounting/periods',\n{$insert}", $content);
        file_put_contents($path, $content);
    }
}
