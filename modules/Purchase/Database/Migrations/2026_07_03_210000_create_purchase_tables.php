<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('purchase_vendors')) { Schema::create('purchase_vendors', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('purchase_orders')) { Schema::create('purchase_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('vendor_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('purchase_bills')) { Schema::create('purchase_bills', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('vendor_id')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('purchase_vendors')) { Schema::create('purchase_vendors', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('purchase_orders')) { Schema::create('purchase_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('vendor_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('purchase_bills')) { Schema::create('purchase_bills', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('vendor_id')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('purchase_bills');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_vendors');
        Schema::dropIfExists('purchase_bills');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_vendors');
    }
};
