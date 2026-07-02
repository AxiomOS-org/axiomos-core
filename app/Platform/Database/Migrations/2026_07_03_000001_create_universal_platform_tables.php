<?php

declare(strict_types=1);

use App\Infrastructure\Database\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universal_tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('color', 32)->nullable();
            $table->string('scope', 128)->default('global');
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('universal_taggables', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tag_id')->constrained('universal_tags')->cascadeOnDelete();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['tag_id', 'entity_type', 'entity_id'], 'universal_taggables_unique');
        });

        Schema::create('universal_labels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('color', 32)->nullable();
            $table->string('scope', 128)->default('global');
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('universal_labelables', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('label_id')->constrained('universal_labels')->cascadeOnDelete();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['label_id', 'entity_type', 'entity_id'], 'universal_labelables_unique');
        });

        Schema::create('universal_audit_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('action', 64);
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->uuid('actor_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index('occurred_at');
        });

        Schema::create('universal_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('type', 64);
            $table->string('title');
            $table->text('description')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->uuid('actor_id')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('type');
        });

        Schema::create('universal_attachments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('disk', 64)->default('local');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type', 128)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('universal_notes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('universal_comments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->uuid('parent_id')->nullable();
            $table->text('body');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_type', 'entity_id']);
            $table->index('parent_id');
        });

        Schema::create('universal_favorites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
            $table->unique(['user_id', 'entity_type', 'entity_id'], 'universal_favorites_unique');
        });

        Schema::create('universal_custom_field_definitions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->string('field_key', 64);
            $table->string('label');
            $table->string('field_type', 32);
            $table->jsonb('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('universal_custom_field_values', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('definition_id')->constrained('universal_custom_field_definitions')->cascadeOnDelete();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->jsonb('value')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['definition_id', 'entity_type', 'entity_id'], 'universal_custom_field_values_unique');
        });

        Schema::create('universal_approval_workflows', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->string('name');
            $table->string('slug');
            $table->jsonb('steps')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('universal_approval_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_id')->constrained('universal_approval_workflows')->cascadeOnDelete();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('status', 32)->default('pending');
            $table->unsignedInteger('current_step')->default(0);
            $table->uuid('requested_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
        });

        Schema::create('universal_approval_steps', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('request_id')->constrained('universal_approval_requests')->cascadeOnDelete();
            $table->unsignedInteger('step_order');
            $table->string('status', 32)->default('pending');
            $table->uuid('approver_id')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('universal_notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('entity_type', 128)->nullable();
            $table->uuid('entity_id')->nullable();
            $table->string('channel', 32)->default('in_app');
            $table->string('title');
            $table->text('body')->nullable();
            $table->jsonb('payload')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
        });

        Schema::create('universal_entity_versions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->unsignedInteger('version_number');
            $table->jsonb('snapshot');
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['entity_type', 'entity_id', 'version_number'], 'universal_entity_versions_unique');
        });

        Schema::create('universal_ai_contexts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('entity_type', 128);
            $table->uuid('entity_id');
            $table->string('context_key', 64)->default('default');
            $table->jsonb('context');
            $table->jsonb('metadata')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['entity_type', 'entity_id', 'context_key'], 'universal_ai_contexts_unique');
        });

        PostgresSchema::partialUniqueIndex(
            'universal_custom_field_definitions',
            'universal_custom_field_definitions_unique',
            'entity_type, field_key',
        );

        PostgresSchema::ginJsonbIndex('universal_audit_logs', 'metadata', 'universal_audit_logs_metadata_gin');
        PostgresSchema::ginJsonbIndex('universal_activities', 'metadata', 'universal_activities_metadata_gin');
        PostgresSchema::ginJsonbIndex('universal_ai_contexts', 'context', 'universal_ai_contexts_context_gin');

        PostgresSchema::addWeightedSearchVector('universal_activities', 'search_vector', [
            'A:coalesce(title, \'\')',
            'B:coalesce(description, \'\')',
        ]);
        PostgresSchema::ginTsVectorIndex('universal_activities', 'search_vector', 'universal_activities_search_gin');

        Schema::table('universal_comments', function (Blueprint $table): void {
            $table->foreign('parent_id')->references('id')->on('universal_comments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universal_ai_contexts');
        Schema::dropIfExists('universal_entity_versions');
        Schema::dropIfExists('universal_notifications');
        Schema::dropIfExists('universal_approval_steps');
        Schema::dropIfExists('universal_approval_requests');
        Schema::dropIfExists('universal_approval_workflows');
        Schema::dropIfExists('universal_custom_field_values');
        Schema::dropIfExists('universal_custom_field_definitions');
        Schema::dropIfExists('universal_favorites');
        Schema::dropIfExists('universal_comments');
        Schema::dropIfExists('universal_notes');
        Schema::dropIfExists('universal_attachments');
        Schema::dropIfExists('universal_activities');
        Schema::dropIfExists('universal_audit_logs');
        Schema::dropIfExists('universal_labelables');
        Schema::dropIfExists('universal_labels');
        Schema::dropIfExists('universal_taggables');
        Schema::dropIfExists('universal_tags');
    }
};

