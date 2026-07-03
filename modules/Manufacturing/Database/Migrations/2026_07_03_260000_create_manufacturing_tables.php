<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('manufacturing_boms')) { Schema::create('manufacturing_boms', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('item_id')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('manufacturing_work_orders')) { Schema::create('manufacturing_work_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('bom_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('quantity', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('manufacturing_production_runs')) { Schema::create('manufacturing_production_runs', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('work_order_id')->nullable();
            $table->string('status')->nullable();
            $table->decimal('quantity_produced', 18, 6)->default(0);
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('manufacturing_boms')) { Schema::create('manufacturing_boms', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('item_id')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('manufacturing_work_orders')) { Schema::create('manufacturing_work_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('bom_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('quantity', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('manufacturing_production_runs')) { Schema::create('manufacturing_production_runs', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('work_order_id')->nullable();
            $table->string('status')->nullable();
            $table->decimal('quantity_produced', 18, 6)->default(0);
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('manufacturing_production_runs');
        Schema::dropIfExists('manufacturing_work_orders');
        Schema::dropIfExists('manufacturing_boms');
        Schema::dropIfExists('manufacturing_production_runs');
        Schema::dropIfExists('manufacturing_work_orders');
        Schema::dropIfExists('manufacturing_boms');
    }
};
