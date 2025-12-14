<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'status',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
