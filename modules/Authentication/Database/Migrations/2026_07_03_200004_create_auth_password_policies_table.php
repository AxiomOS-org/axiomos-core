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
        Schema::create('auth_password_policies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->jsonb('rules')->nullable();
            $table->unsignedInteger('min_length')->default(12);
            $table->unsignedInteger('expiry_days')->default(90);
            $table->unsignedInteger('history_count')->default(5);
            $table->unsignedInteger('lockout_threshold')->default(5);
            $table->unsignedInteger('lockout_minutes')->default(15);
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
        });

        PostgresSchema::ginJsonbIndex('auth_password_policies', 'rules', 'auth_password_policies_rules_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_password_policies');
    }
};
