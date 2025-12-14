<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'source',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'meta',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
