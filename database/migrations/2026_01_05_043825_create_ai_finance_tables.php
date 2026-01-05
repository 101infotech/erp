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
        // Drop tables if they exist (cleanup from previous failed migration)
        Schema::dropIfExists('ai_finance_predictions');
        Schema::dropIfExists('ai_finance_anomaly_detections');
        Schema::dropIfExists('ai_finance_category_patterns');
        Schema::dropIfExists('ai_finance_category_predictions');

        // AI Category Predictions Table
        Schema::create('ai_finance_category_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('finance_transactions')->onDelete('cascade');
            $table->foreignId('suggested_category_id')->constrained('finance_categories')->onDelete('cascade');
            $table->decimal('confidence_score', 5, 4)->comment('0.0000 to 1.0000');
            $table->text('reasoning')->nullable()->comment('AI explanation for suggestion');
            $table->json('alternative_categories')->nullable()->comment('Array of alternative category IDs');
            $table->boolean('was_accepted')->nullable()->comment('NULL=pending, true=accepted, false=rejected');
            $table->string('ai_provider', 50)->default('openai')->comment('openai, anthropic, etc');
            $table->string('ai_model', 50)->default('gpt-4');
            $table->timestamps();

            $table->index(['transaction_id', 'was_accepted'], 'ai_cat_pred_txn_accepted_idx');
            $table->index('confidence_score', 'ai_cat_pred_confidence_idx');
        });

        // AI Category Learning Patterns Table
        Schema::create('ai_finance_category_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->enum('pattern_type', ['vendor', 'customer', 'description', 'amount_range', 'payment_method']);
            $table->text('pattern_value')->comment('The pattern string/value');
            $table->foreignId('category_id')->constrained('finance_categories')->onDelete('cascade');
            $table->integer('occurrence_count')->default(1)->comment('How many times this pattern appeared');
            $table->decimal('success_rate', 5, 4)->default(1.0000)->comment('How often this pattern correctly predicted');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'pattern_type']);
            $table->index('success_rate');
        });

        // AI Anomaly Detections Table
        Schema::create('ai_finance_anomaly_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('finance_transactions')->onDelete('cascade');
            $table->enum('anomaly_type', ['amount', 'duplicate', 'timing', 'velocity', 'pattern', 'fraud_risk']);
            $table->integer('risk_score')->comment('0-100');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->string('description', 500);
            $table->text('ai_reasoning')->nullable()->comment('Detailed AI explanation');
            $table->timestamp('flagged_at')->useCurrent();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->enum('review_status', ['pending', 'approved', 'rejected', 'needs_investigation'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['flagged_at', 'review_status']);
            $table->index('risk_score');
            $table->index('severity');
        });

        // AI Financial Predictions Table
        Schema::create('ai_finance_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->enum('prediction_type', ['cashflow', 'budget', 'revenue', 'expense', 'trend']);
            $table->string('period_start_bs', 10)->comment('Nepali BS date');
            $table->string('period_end_bs', 10)->comment('Nepali BS date');
            $table->decimal('predicted_amount', 15, 2);
            $table->decimal('confidence_level', 5, 4)->comment('0.0000 to 1.0000');
            $table->decimal('actual_amount', 15, 2)->nullable()->comment('Filled when period completes');
            $table->decimal('accuracy_score', 5, 4)->nullable()->comment('Calculated after actual known');
            $table->json('factors')->nullable()->comment('AI reasoning and contributing factors');
            $table->string('ai_model', 50)->default('gpt-4');
            $table->timestamps();

            $table->index(['company_id', 'prediction_type']);
            $table->index(['period_start_bs', 'period_end_bs']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_finance_predictions');
        Schema::dropIfExists('ai_finance_anomaly_detections');
        Schema::dropIfExists('ai_finance_category_patterns');
        Schema::dropIfExists('ai_finance_category_predictions');
    }
};
