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
        Schema::create('auth_oauth_tokens', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('oauth_client_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('access_token_hash');
            $table->string('refresh_token_hash')->nullable();
            $table->jsonb('scopes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('oauth_client_id')->references('id')->on('auth_oauth_clients')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['oauth_client_id', 'user_id', 'status']);
        });

        PostgresSchema::partialUniqueIndex('auth_oauth_tokens', 'auth_oauth_tokens_access_token_hash_unique', 'access_token_hash');
        PostgresSchema::partialUniqueIndex('auth_oauth_tokens', 'auth_oauth_tokens_refresh_token_hash_unique', 'refresh_token_hash');
        PostgresSchema::ginJsonbIndex('auth_oauth_tokens', 'scopes', 'auth_oauth_tokens_scopes_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_tokens');
    }
};
