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
        Schema::create('branches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
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

        PostgresSchema::partialUniqueIndex('branches', 'branches_company_id_code_unique', 'company_id, code');
        PostgresSchema::partialUniqueIndex('branches', 'branches_company_id_slug_unique', 'company_id, slug');
        PostgresSchema::partialIndex(
            'branches',
            'branches_status_active_idx',
            'status',
            "deleted_at IS NULL AND status = 'active'",
        );
        PostgresSchema::ginJsonbIndex('branches', 'settings', 'branches_settings_gin');
        PostgresSchema::addWeightedSearchVector('branches', 'search_vector', [
            'A:coalesce(name, \'\')',
            'B:coalesce(code, \'\')',
            'C:coalesce(description, \'\')',
        ]);
        PostgresSchema::ginTsVectorIndex('branches', 'search_vector', 'branches_search_vector_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
