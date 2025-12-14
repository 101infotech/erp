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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Finance, Education, Healthcare, Travel, Logistics
            $table->string('category_color', 7)->default('#4169E1'); // Hex color
            $table->integer('completed_tasks')->default(0);
            $table->integer('total_tasks')->default(0);
            $table->decimal('budget', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, completed, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
