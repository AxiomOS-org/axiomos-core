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
        Schema::create('identities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->string('identity_type', 64);
            $table->string('code', 64);
            $table->string('display_name');
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('status', 32)->default('active');
            $table->jsonb('metadata')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index(['organization_id', 'status']);
            $table->index('identity_type');
            $table->index('status');
        });

        PostgresSchema::partialUniqueIndex('identities', 'identities_code_unique', 'code');
        PostgresSchema::ginJsonbIndex('identities', 'metadata', 'identities_metadata_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('identities');
    }
};
