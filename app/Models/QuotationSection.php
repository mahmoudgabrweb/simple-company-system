<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationSection extends Model
{
    protected $fillable = [
        'quotation_id', 'letter_code', 'title', 'description', 'is_static', 'order', 'total_amount'
    ];

    protected $casts = [
        'is_static' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->whereNull('parent_item_id')->orderBy('order');
    }

    public function allItems()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('order');
    }

    public function recomputeTotals(): void
    {
        $sum = $this->allItems()->where('is_excluded', false)->sum('total_price');
        $this->forceFill(['total_amount' => round((float)$sum, 2)])->saveQuietly();
        $this->quotation->recomputeTotals();
    }
}
