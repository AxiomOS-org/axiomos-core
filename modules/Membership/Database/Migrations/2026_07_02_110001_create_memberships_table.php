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
        Schema::create('memberships', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('organization_id');
            $table->string('membership_type', 32);
            $table->string('status', 32)->default('active');
            $table->jsonb('scopes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['user_id', 'organization_id']);
            $table->index(['organization_id', 'status']);
            $table->index('membership_type');
        });

        PostgresSchema::partialUniqueIndex(
            'memberships',
            'memberships_user_organization_unique',
            'user_id, organization_id',
        );
        PostgresSchema::ginJsonbIndex('memberships', 'scopes', 'memberships_scopes_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
