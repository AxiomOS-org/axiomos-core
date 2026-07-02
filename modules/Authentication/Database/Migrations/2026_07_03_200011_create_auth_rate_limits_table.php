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
        Schema::create('auth_rate_limits', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('rate_key', 191);
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('window_start')->nullable();
            $table->timestamp('blocked_until')->nullable();
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        PostgresSchema::partialUniqueIndex('auth_rate_limits', 'auth_rate_limits_rate_key_unique', 'rate_key');
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_rate_limits');
    }
};
