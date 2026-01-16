<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    protected $table = 'lead_stages';

    protected $fillable = [
        'stage_number',
        'stage_name',
        'description',
        'auto_timeout_days',
        'requires_action',
        'notification_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_action' => 'boolean',
        'auto_timeout_days' => 'integer',
    ];

    /**
     * Get the leads for this stage
     */
    public function leads()
    {
        return $this->hasMany(ServiceLead::class, 'lead_stage_id');
    }

    /**
     * Scope to get only active stages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('stage_number');
    }

    /**
     * Get stage by number
     */
    public static function getByNumber($stageNumber)
    {
        return static::where('stage_number', $stageNumber)->active()->first();
    }

    /**
     * Get stage name by number
     */
    public static function getStageName($stageNumber)
    {
        $stage = static::getByNumber($stageNumber);
        return $stage?->stage_name ?? 'Unknown';
    }
}
