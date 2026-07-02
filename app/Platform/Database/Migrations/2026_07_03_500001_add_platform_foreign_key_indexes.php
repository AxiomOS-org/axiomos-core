<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universal_approval_requests', function (Blueprint $table): void {
            $table->index('workflow_id');
        });

        Schema::table('universal_approval_steps', function (Blueprint $table): void {
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('universal_approval_steps', function (Blueprint $table): void {
            $table->dropIndex(['request_id']);
        });

        Schema::table('universal_approval_requests', function (Blueprint $table): void {
            $table->dropIndex(['workflow_id']);
        });
    }
};
