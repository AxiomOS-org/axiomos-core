<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('fixed_assets')) { Schema::create('fixed_assets', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('asset_code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->decimal('acquisition_cost', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('fixed_asset_depreciation_runs')) { Schema::create('fixed_asset_depreciation_runs', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('period_label')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('fixed_assets')) { Schema::create('fixed_assets', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('asset_code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->decimal('acquisition_cost', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('fixed_asset_depreciation_runs')) { Schema::create('fixed_asset_depreciation_runs', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('period_label')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('fixed_asset_depreciation_runs');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('fixed_asset_depreciation_runs');
        Schema::dropIfExists('fixed_assets');
    }
};
