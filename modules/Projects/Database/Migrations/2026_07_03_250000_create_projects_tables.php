<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('projects_projects')) { Schema::create('projects_projects', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->decimal('budget_amount', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('projects_tasks')) { Schema::create('projects_tasks', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->nullable();
            $table->uuid('assignee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('projects_timesheets')) { Schema::create('projects_timesheets', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->uuid('employee_id')->nullable();
            $table->timestamp('work_date')->nullable();
            $table->decimal('hours', 18, 6)->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('projects_projects')) { Schema::create('projects_projects', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->decimal('budget_amount', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('projects_tasks')) { Schema::create('projects_tasks', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->nullable();
            $table->uuid('assignee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('projects_timesheets')) { Schema::create('projects_timesheets', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->uuid('employee_id')->nullable();
            $table->timestamp('work_date')->nullable();
            $table->decimal('hours', 18, 6)->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('projects_timesheets');
        Schema::dropIfExists('projects_tasks');
        Schema::dropIfExists('projects_projects');
        Schema::dropIfExists('projects_timesheets');
        Schema::dropIfExists('projects_tasks');
        Schema::dropIfExists('projects_projects');
    }
};
