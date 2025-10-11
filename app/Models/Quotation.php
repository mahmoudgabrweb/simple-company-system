<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'company_id', 'project_id', 'quotation_number', 'version', 'status', 'is_active',
        'total_amount', 'notes', 'created_by', 'approved_by', 'approved_at', 'valid_until',
        'sent_at', 'pdf_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
    ];

    public function sections()
    {
        return $this->hasMany(QuotationSection::class)->orderBy('order');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // recompute grand total from included items across all sections
    public function recomputeTotals(): void
    {
        $sum = QuotationItem::whereIn('quotation_section_id', $this->sections()->pluck('id'))
            ->where('is_excluded', false)
            ->sum('total_price');

        $this->forceFill(['total_amount' => round((float)$sum, 2)])->saveQuietly();
    }
}
