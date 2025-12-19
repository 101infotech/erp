<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedbackPrompt extends Model
{
    use HasFactory;

    protected $table = 'ai_feedback_prompts';

    protected $fillable = [
        'user_id',
        'feedback_id',
        'prompt',
        'context',
        'category',
        'sequence_number',
        'ai_metadata',
        'is_used',
    ];

    protected $casts = [
        'context' => 'array',
        'ai_metadata' => 'array',
        'is_used' => 'boolean',
    ];

    /**
     * Get the user who this prompt was generated for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the feedback this prompt was used in
     */
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(EmployeeFeedback::class);
    }

    /**
     * Mark this prompt as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Get all unused prompts for a user
     */
    public static function getUnusedPrompts($userId, $limit = null)
    {
        $query = static::where('user_id', $userId)
            ->where('is_used', false)
            ->orderBy('sequence_number');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get the latest prompts generated for a user
     */
    public static function getLatestForUser($userId, $limit = 3)
    {
        return static::where('user_id', $userId)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
