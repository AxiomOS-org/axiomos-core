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
        Schema::create('companies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('code', 64);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('logo')->nullable();
            $table->string('status', 32)->default('active');
            $table->string('timezone', 64)->default('UTC');
            $table->string('currency', 8)->default('USD');
            $table->string('locale', 16)->default('en');
            $table->string('country', 8)->default('US');
            $table->jsonb('settings')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        PostgresSchema::partialUniqueIndex('companies', 'companies_organization_id_code_unique', 'organization_id, code');
        PostgresSchema::partialUniqueIndex('companies', 'companies_organization_id_slug_unique', 'organization_id, slug');
        PostgresSchema::partialIndex(
            'companies',
            'companies_status_active_idx',
            'status',
            "deleted_at IS NULL AND status = 'active'",
        );
        PostgresSchema::ginJsonbIndex('companies', 'settings', 'companies_settings_gin');
        PostgresSchema::addWeightedSearchVector('companies', 'search_vector', [
            'A:coalesce(name, \'\')',
            'B:coalesce(code, \'\')',
            'C:coalesce(description, \'\')',
        ]);
        PostgresSchema::ginTsVectorIndex('companies', 'search_vector', 'companies_search_vector_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
