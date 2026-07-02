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
        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('identity_id');
            $table->string('username', 100);
            $table->string('email');
            $table->string('display_name');
            $table->string('status', 32)->default('active');
            $table->jsonb('settings')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('identity_id')
                ->references('id')
                ->on('identities')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['identity_id', 'status']);
            $table->index('status');
        });

        PostgresSchema::partialUniqueIndex('users', 'users_username_unique', 'username');
        PostgresSchema::partialUniqueIndex('users', 'users_email_unique', 'email');
        PostgresSchema::ginJsonbIndex('users', 'settings', 'users_settings_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
