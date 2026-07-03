<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('hr_employees')) { Schema::create('hr_employees', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('hr_attendance_records')) { Schema::create('hr_attendance_records', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('employee_id')->nullable();
            $table->timestamp('work_date')->nullable();
            $table->string('status')->nullable();
            $table->decimal('hours_worked', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('hr_payroll_runs')) { Schema::create('hr_payroll_runs', static function (Blueprint $table): void {
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

        if (! Schema::hasTable('hr_employees')) { Schema::create('hr_employees', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('hr_attendance_records')) { Schema::create('hr_attendance_records', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('employee_id')->nullable();
            $table->timestamp('work_date')->nullable();
            $table->string('status')->nullable();
            $table->decimal('hours_worked', 18, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('hr_payroll_runs')) { Schema::create('hr_payroll_runs', static function (Blueprint $table): void {
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
        Schema::dropIfExists('hr_payroll_runs');
        Schema::dropIfExists('hr_attendance_records');
        Schema::dropIfExists('hr_employees');
        Schema::dropIfExists('hr_payroll_runs');
        Schema::dropIfExists('hr_attendance_records');
        Schema::dropIfExists('hr_employees');
    }
};
