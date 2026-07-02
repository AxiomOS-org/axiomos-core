<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->index('leader_identity_id');
        });

        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->index('identity_id');
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->dropIndex(['department_id']);
            $table->dropIndex(['identity_id']);
        });

        Schema::table('teams', function (Blueprint $table): void {
            $table->dropIndex(['leader_identity_id']);
        });
    }
};
