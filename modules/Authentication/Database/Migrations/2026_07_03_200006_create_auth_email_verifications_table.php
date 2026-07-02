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
        Schema::create('auth_email_verifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('token_hash');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->string('status', 32)->default('pending');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['user_id', 'expires_at']);
        });

        PostgresSchema::partialUniqueIndex('auth_email_verifications', 'auth_email_verifications_token_hash_unique', 'token_hash');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_email_verifications');
    }
};
