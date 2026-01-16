<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDocument extends Model
{
    use SoftDeletes;

    protected $table = 'lead_documents';

    protected $fillable = [
        'lead_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by_id',
    ];

    /**
     * Get the lead this document belongs to
     */
    public function lead()
    {
        return $this->belongsTo(ServiceLead::class, 'lead_id');
    }

    /**
     * Get the user who uploaded this document
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    /**
     * Scope to get documents for a specific lead
     */
    public function scopeForLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    /**
     * Scope to get documents by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get documents uploaded by a specific user
     */
    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('uploaded_by_id', $userId);
    }

    /**
     * Get document type label
     */
    public function getTypeLabel()
    {
        return match ($this->document_type) {
            'photo' => 'Photo',
            'design' => 'Design',
            'contract' => 'Contract',
            'quotation' => 'Quotation',
            'report' => 'Report',
            'other' => 'Other',
            default => ucfirst($this->document_type),
        };
    }

    /**
     * Get file size in human-readable format
     */
    public function getFileSizeFormatted()
    {
        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $i < count($units) && $size >= 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getFileExtension()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
