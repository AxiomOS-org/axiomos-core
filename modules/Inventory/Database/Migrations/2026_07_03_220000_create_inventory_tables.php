<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('inventory_warehouses')) { Schema::create('inventory_warehouses', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('inventory_items')) { Schema::create('inventory_items', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('inventory_stock_movements')) { Schema::create('inventory_stock_movements', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('warehouse_id')->nullable();
            $table->uuid('item_id')->nullable();
            $table->string('movement_type')->nullable();
            $table->decimal('quantity', 18, 6)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('inventory_warehouses')) { Schema::create('inventory_warehouses', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('inventory_items')) { Schema::create('inventory_items', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('inventory_stock_movements')) { Schema::create('inventory_stock_movements', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('warehouse_id')->nullable();
            $table->uuid('item_id')->nullable();
            $table->string('movement_type')->nullable();
            $table->decimal('quantity', 18, 6)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('inventory_stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_warehouses');
        Schema::dropIfExists('inventory_stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_warehouses');
    }
};
