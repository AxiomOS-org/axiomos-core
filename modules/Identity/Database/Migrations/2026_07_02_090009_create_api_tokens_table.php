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
        Schema::create('api_tokens', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('identity_id');
            $table->string('name', 120);
            $table->string('token_hash');
            $table->jsonb('scopes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('identity_id')->references('id')->on('identities')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['identity_id', 'status']);
        });

        PostgresSchema::ginJsonbIndex('api_tokens', 'scopes', 'api_tokens_scopes_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
