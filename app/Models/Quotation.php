<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'project_id', 'code', 'version', 'status', 'is_active',
        'issue_date', 'valid_until', 'sent_at', 'approved_at', 'completed_at',
        'vat_rate', 'subtotal', 'discount_amount', 'tax_total', 'grand_total', 'pdf_path'
    ];

    public const ST_DRAFT = 'draft';
    public const ST_SENDING = 'sending_to_client';
    public const ST_WAITING = 'waiting_client_response';
    public const ST_APPROVED = 'client_approved';
    public const ST_INPROGRESS = 'in_progress';
    public const ST_COMPLETED = 'completed';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('order');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
