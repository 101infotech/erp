<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'event_date',
        'title',
        'description',
        'priority',
        'team_members',
    ];

    protected $casts = [
        'event_date' => 'date',
        'team_members' => 'array',
    ];

    /**
     * Get the project for this event
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the team members with user details
     */
    public function getTeamMembersWithDetailsAttribute()
    {
        if (!$this->team_members) {
            return [];
        }
        return User::whereIn('id', $this->team_members)
            ->select('id', 'name', 'email')
            ->get();
    }
}
