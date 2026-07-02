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
        Schema::create('auth_security_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('event_type', 120);
            $table->uuid('user_id')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('geo')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->string('severity', 32)->default('info');
            $table->string('status', 32)->default('recorded');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['event_type', 'severity', 'created_at']);
        });

        PostgresSchema::ginJsonbIndex('auth_security_events', 'geo', 'auth_security_events_geo_gin');
        PostgresSchema::ginJsonbIndex('auth_security_events', 'metadata', 'auth_security_events_metadata_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_security_events');
    }
};
