<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'category_color',
        'completed_tasks',
        'total_tasks',
        'budget',
        'description',
        'status',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'completed_tasks' => 'integer',
        'total_tasks' => 'integer',
    ];

    protected $appends = [
        'progress_percentage',
        'member_count',
    ];

    /**
     * Get the team members for this project
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the calendar events for this project
     */
    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Get the progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_tasks == 0) {
            return 0;
        }
        return round(($this->completed_tasks / $this->total_tasks) * 100, 2);
    }

    /**
     * Get the member count
     */
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }
}
