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
        Schema::create('auth_trusted_devices', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('device_fingerprint');
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->jsonb('geo')->nullable();
            $table->timestamp('trusted_until')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['user_id', 'trusted_until']);
        });

        PostgresSchema::partialUniqueIndex('auth_trusted_devices', 'auth_trusted_devices_user_fingerprint_unique', 'user_id, device_fingerprint');
        PostgresSchema::ginJsonbIndex('auth_trusted_devices', 'geo', 'auth_trusted_devices_geo_gin');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_trusted_devices');
    }
};
