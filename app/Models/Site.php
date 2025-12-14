<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function newsMedia(): HasMany
    {
        return $this->hasMany(NewsMedia::class);
    }

    public function careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(ContactForm::class);
    }

    public function bookingForms(): HasMany
    {
        return $this->hasMany(BookingForm::class);
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scheduleMeetings(): HasMany
    {
        return $this->hasMany(ScheduleMeeting::class);
    }

    public function hirings(): HasMany
    {
        return $this->hasMany(Hiring::class);
    }

    public function companiesList(): HasMany
    {
        return $this->hasMany(CompanyList::class);
    }
}
