<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('purchase_number', 50)->unique();
            $table->string('purchase_date_bs', 10);
            $table->foreignId('vendor_id')->nullable()->constrained('finance_vendors')->onDelete('set null');
            $table->string('vendor_name');
            $table->string('vendor_pan')->nullable();
            $table->text('vendor_address')->nullable();
            $table->string('bill_number')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->decimal('tds_percentage', 5, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_date_bs', 10)->nullable();
            $table->text('description')->nullable();
            $table->string('fiscal_year_bs', 4);
            $table->string('document_path')->nullable()->comment('Bill PDF/image');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('company_id');
            $table->index('purchase_date_bs');
            $table->index('payment_status');
            $table->index('fiscal_year_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_purchases');
    }
};
