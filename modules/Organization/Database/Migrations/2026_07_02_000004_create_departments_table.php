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
        Schema::create('departments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
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
            $table->index('parent_id');
        });

        Schema::table('departments', function (Blueprint $table): void {
            $table->foreign('parent_id')->references('id')->on('departments')->nullOnDelete();
        });

        PostgresSchema::partialUniqueIndex('departments', 'departments_branch_id_code_unique', 'branch_id, code');
        PostgresSchema::partialUniqueIndex('departments', 'departments_branch_id_slug_unique', 'branch_id, slug');
        PostgresSchema::partialIndex(
            'departments',
            'departments_status_active_idx',
            'status',
            "deleted_at IS NULL AND status = 'active'",
        );
        PostgresSchema::partialIndex(
            'departments',
            'departments_root_only_idx',
            'branch_id',
            'deleted_at IS NULL AND parent_id IS NULL',
        );
        PostgresSchema::ginJsonbIndex('departments', 'settings', 'departments_settings_gin');
        PostgresSchema::addWeightedSearchVector('departments', 'search_vector', [
            'A:coalesce(name, \'\')',
            'B:coalesce(code, \'\')',
            'C:coalesce(description, \'\')',
        ]);
        PostgresSchema::ginTsVectorIndex('departments', 'search_vector', 'departments_search_vector_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
