<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if a named index exists on a given table.
     */
    protected function indexExists(string $table, string $index): bool
    {
        $dbName = DB::getDatabaseName();
        $result = DB::select(
            'SELECT COUNT(1) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$dbName, $table, $index]
        );
        return !empty($result) && ((int)($result[0]->cnt ?? 0)) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ai_feedback_sentiment_analysis')) {
            return; // table missing; nothing to do
        }

        // Create short-named indexes if they don't exist already
        if (!$this->indexExists('ai_feedback_sentiment_analysis', 'afsa_user_created_idx')) {
            Schema::table('ai_feedback_sentiment_analysis', function ($table) {
                $table->index(['user_id', 'created_at'], 'afsa_user_created_idx');
            });
        }

        if (!$this->indexExists('ai_feedback_sentiment_analysis', 'afsa_class_created_idx')) {
            Schema::table('ai_feedback_sentiment_analysis', function ($table) {
                $table->index(['overall_classification', 'created_at'], 'afsa_class_created_idx');
            });
        }

        if (!$this->indexExists('ai_feedback_sentiment_analysis', 'afsa_attention_created_idx')) {
            Schema::table('ai_feedback_sentiment_analysis', function ($table) {
                $table->index(['needs_manager_attention', 'created_at'], 'afsa_attention_created_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ai_feedback_sentiment_analysis')) {
            return;
        }

        Schema::table('ai_feedback_sentiment_analysis', function ($table) {
            // Drop short-named indexes if present
            $table->dropIndex('afsa_user_created_idx');
            $table->dropIndex('afsa_class_created_idx');
            $table->dropIndex('afsa_attention_created_idx');
        });
    }
};
