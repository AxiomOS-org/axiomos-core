<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('reporting_definitions')) { Schema::create('reporting_definitions', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('report_type')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('reporting_snapshots')) { Schema::create('reporting_snapshots', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('report_definition_id')->nullable();
            $table->timestamp('snapshot_date')->nullable();
            $table->string('status')->nullable();
            $table->jsonb('payload_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('reporting_definitions')) { Schema::create('reporting_definitions', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('report_type')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('reporting_snapshots')) { Schema::create('reporting_snapshots', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('report_definition_id')->nullable();
            $table->timestamp('snapshot_date')->nullable();
            $table->string('status')->nullable();
            $table->jsonb('payload_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('reporting_snapshots');
        Schema::dropIfExists('reporting_definitions');
        Schema::dropIfExists('reporting_snapshots');
        Schema::dropIfExists('reporting_definitions');
    }
};
