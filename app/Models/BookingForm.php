<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'website_url',
        'industry',
        'services_interested',
        'investment_range',
        'flight_timeline',
        'marketing_goals',
        'current_challenges',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'services_interested' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
