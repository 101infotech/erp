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
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('finance_companies')->onDelete('cascade')->comment('NULL = applies to all companies');
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'both'])->default('expense');
            $table->foreignId('parent_category_id')->nullable()->constrained('finance_categories')->onDelete('set null');
            $table->string('color_code', 7)->nullable()->comment('Hex color for UI');
            $table->string('icon', 50)->nullable();
            $table->boolean('is_system')->default(false)->comment('Cannot be deleted if true');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('company_id');
            $table->index('type');
            $table->index('is_active');
            $table->index('parent_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_categories');
    }
};
