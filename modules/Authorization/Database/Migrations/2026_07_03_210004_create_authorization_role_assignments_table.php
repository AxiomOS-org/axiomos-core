<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorization_role_assignments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->string('assignable_type', 180);
            $table->uuid('assignable_id');
            $table->uuid('organization_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('authorization_roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['assignable_type', 'assignable_id']);
            $table->unique(['role_id', 'assignable_type', 'assignable_id', 'organization_id'], 'authorization_role_assignments_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorization_role_assignments');
    }
};
