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
        Schema::create('auth_oauth_clients', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('client_id', 120);
            $table->string('client_secret_hash');
            $table->string('name', 120);
            $table->jsonb('redirect_uris')->nullable();
            $table->jsonb('scopes')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        PostgresSchema::partialUniqueIndex('auth_oauth_clients', 'auth_oauth_clients_client_id_unique', 'client_id');
        PostgresSchema::ginJsonbIndex('auth_oauth_clients', 'redirect_uris', 'auth_oauth_clients_redirect_uris_gin');
        PostgresSchema::ginJsonbIndex('auth_oauth_clients', 'scopes', 'auth_oauth_clients_scopes_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_clients');
    }
};
