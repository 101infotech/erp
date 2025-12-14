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
        Schema::create('hirings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('position');
            $table->string('department')->nullable();
            $table->enum('type', ['full-time', 'part-time', 'contract', 'internship'])->default('full-time');
            $table->string('location')->nullable();
            $table->text('description');
            $table->json('requirements')->nullable(); // Array of requirements
            $table->json('responsibilities')->nullable(); // Array of responsibilities
            $table->string('salary_range')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('vacancies')->default(1);
            $table->enum('status', ['open', 'closed', 'filled'])->default('open');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hirings');
    }
};
