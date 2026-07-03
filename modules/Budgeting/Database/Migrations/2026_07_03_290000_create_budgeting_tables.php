<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('budget_versions')) { Schema::create('budget_versions', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('fiscal_year')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('budget_lines')) { Schema::create('budget_lines', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('budget_version_id')->nullable();
            $table->uuid('account_id')->nullable();
            $table->string('period_label')->nullable();
            $table->decimal('amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('budget_versions')) { Schema::create('budget_versions', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('fiscal_year')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('budget_lines')) { Schema::create('budget_lines', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('budget_version_id')->nullable();
            $table->uuid('account_id')->nullable();
            $table->string('period_label')->nullable();
            $table->decimal('amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budget_versions');
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budget_versions');
    }
};
