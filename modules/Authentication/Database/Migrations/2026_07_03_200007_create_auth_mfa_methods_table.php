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
        Schema::create('auth_mfa_methods', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('method_type', 32)->default('totp');
            $table->text('secret_encrypted');
            $table->boolean('enabled')->default(true);
            $table->jsonb('recovery_codes')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['user_id', 'method_type', 'enabled']);
        });

        PostgresSchema::ginJsonbIndex('auth_mfa_methods', 'recovery_codes', 'auth_mfa_methods_recovery_codes_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_methods');
    }
};
