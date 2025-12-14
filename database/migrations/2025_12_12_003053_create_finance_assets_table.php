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
        Schema::create('finance_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->onDelete('set null');

            // Asset Identification
            $table->string('asset_number')->unique(); // Auto-generated: AST-YYYY-XXXXXX
            $table->string('asset_name');
            $table->text('description')->nullable();
            $table->string('asset_type'); // tangible, intangible
            $table->string('asset_category'); // vehicle, equipment, furniture, building, land, software, etc.

            // Purchase Information
            $table->decimal('purchase_cost', 15, 2);
            $table->string('purchase_date_bs'); // Nepali date
            $table->string('fiscal_year_bs', 10);
            $table->string('vendor_name')->nullable();
            $table->string('invoice_number')->nullable();

            // Depreciation Settings
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'sum_of_years', 'units_of_production', 'none'])->default('straight_line');
            $table->integer('useful_life_years')->nullable(); // Expected life in years
            $table->integer('useful_life_months')->nullable(); // For more precise calculation
            $table->decimal('salvage_value', 15, 2)->default(0); // Residual value
            $table->decimal('depreciation_rate', 5, 2)->nullable(); // For declining balance

            // Current Value Tracking
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('book_value', 15, 2); // Purchase cost - Accumulated depreciation
            $table->string('depreciation_start_date_bs')->nullable();

            // Location & Assignment
            $table->string('location')->nullable();
            $table->string('assigned_to')->nullable(); // Employee name or department
            $table->string('serial_number')->nullable();
            $table->string('barcode')->nullable();

            // Status & Disposal
            $table->enum('status', ['active', 'disposed', 'sold', 'transferred', 'under_maintenance', 'written_off'])->default('active');
            $table->string('disposal_date_bs')->nullable();
            $table->decimal('disposal_value', 15, 2)->nullable();
            $table->text('disposal_reason')->nullable();

            // Maintenance
            $table->string('last_maintenance_date_bs')->nullable();
            $table->string('next_maintenance_date_bs')->nullable();
            $table->decimal('total_maintenance_cost', 15, 2)->default(0);

            // Transfer tracking
            $table->foreignId('transferred_to_company_id')->nullable()->constrained('finance_companies')->onDelete('set null');
            $table->string('transfer_date_bs')->nullable();

            // Documents
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('asset_number');
            $table->index('status');
            $table->index('fiscal_year_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_assets');
    }
};
