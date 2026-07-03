<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('sales_customers')) { Schema::create('sales_customers', static function (Blueprint $table): void {
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

        if (! Schema::hasTable('sales_orders')) { Schema::create('sales_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('sales_invoices')) { Schema::create('sales_invoices', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('sales_customers')) { Schema::create('sales_customers', static function (Blueprint $table): void {
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

        if (! Schema::hasTable('sales_orders')) { Schema::create('sales_orders', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

        if (! Schema::hasTable('sales_invoices')) { Schema::create('sales_invoices', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_amount', 18, 6)->default(0);
            $table->string('currency')->nullable();
            $table->uuid('journal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); }

    }
    public function down(): void {
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_customers');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_customers');
    }
};
