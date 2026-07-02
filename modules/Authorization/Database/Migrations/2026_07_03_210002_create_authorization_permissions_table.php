<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorization_permissions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug', 160);
            $table->string('name', 180);
            $table->string('module', 80);
            $table->string('action', 80);
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status', 32)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('slug');
            $table->index(['module', 'action']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorization_permissions');
    }
};
