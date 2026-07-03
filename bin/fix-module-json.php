<?php

declare(strict_types=1);

$definitions = [
    'Sales' => ['priority' => 200, 'description' => 'Sales — customers, orders, invoices, posting via Accounting engine', 'dependencies' => ['Core', 'Organization', 'Accounting']],
    'Purchase' => ['priority' => 210, 'description' => 'Purchase — vendors, POs, GRN, bills, posting via Accounting engine', 'dependencies' => ['Core', 'Organization', 'Accounting']],
    'Inventory' => ['priority' => 220, 'description' => 'Inventory — warehouses, items, stock movements and balances', 'dependencies' => ['Core', 'Organization', 'Accounting']],
    'HR' => ['priority' => 230, 'description' => 'HR & Payroll — employees, attendance, leave, payroll runs', 'dependencies' => ['Core', 'Organization', 'Accounting', 'Identity']],
    'CRM' => ['priority' => 240, 'description' => 'CRM — leads, opportunities, activities', 'dependencies' => ['Core', 'Organization', 'Sales']],
    'Projects' => ['priority' => 250, 'description' => 'Projects — projects, tasks, timesheets', 'dependencies' => ['Core', 'Organization', 'HR']],
    'Manufacturing' => ['priority' => 260, 'description' => 'Manufacturing — BOM, work orders, production runs', 'dependencies' => ['Core', 'Organization', 'Inventory', 'Accounting']],
    'POS' => ['priority' => 270, 'description' => 'POS — terminals, sessions, orders', 'dependencies' => ['Core', 'Organization', 'Sales', 'Inventory', 'Accounting']],
    'FixedAssets' => ['priority' => 280, 'description' => 'Fixed Assets — asset register and depreciation', 'dependencies' => ['Core', 'Organization', 'Accounting']],
    'Budgeting' => ['priority' => 290, 'description' => 'Budgeting — budget versions and lines', 'dependencies' => ['Core', 'Organization', 'Accounting']],
    'Reporting' => ['priority' => 300, 'description' => 'Reporting & BI — read-only financial dashboards', 'dependencies' => ['Core', 'Organization', 'Accounting']],
];

foreach ($definitions as $module => $def) {
    $path = dirname(__DIR__) . "/modules/{$module}/module.json";
    $payload = [
        'name' => $module,
        'version' => '1.0.0',
        'description' => $def['description'],
        'provider' => "Modules\\{$module}\\Providers\\{$module}ServiceProvider",
        'enabled' => true,
        'priority' => $def['priority'],
        'dependencies' => $def['dependencies'],
        'authors' => [['name' => 'AxiomOS Team']],
        'minimumCoreVersion' => '1.0.0',
    ];
    file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    echo "Updated {$module}\n";
}
