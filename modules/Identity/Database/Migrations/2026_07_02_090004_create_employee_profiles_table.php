<?php

declare(strict_types=1);

use App\Infrastructure\Database\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('identity_id');
            $table->uuid('organization_id');
            $table->string('employee_number', 64);
            $table->string('job_title')->nullable();
            $table->uuid('department_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('status', 32)->default('active');
            $table->jsonb('metadata')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('identity_id')->references('id')->on('identities')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['organization_id', 'status']);
            $table->unique(['organization_id', 'employee_number']);
        });

        PostgresSchema::ginJsonbIndex('employee_profiles', 'metadata', 'employee_profiles_metadata_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
