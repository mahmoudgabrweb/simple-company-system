<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_section_id', 'parent_item_id', 'item_number', 'title', 'description',
        'item_type', 'quantity', 'unit_id', 'unit_price', 'total_price', 'metadata', 'order', 'is_excluded'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_excluded' => 'boolean',
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function section()
    {
        return $this->belongsTo(QuotationSection::class, 'quotation_section_id');
    }

    public function parent()
    {
        return $this->belongsTo(QuotationItem::class, 'parent_item_id');
    }

    public function children()
    {
        return $this->hasMany(QuotationItem::class, 'parent_item_id')->orderBy('order');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function details()
    {
        return $this->hasMany(QuotationItemDetail::class)->orderBy('order');
    }

    // compute own total (lump if quantity null; else qty*unit_price)
    public function computeTotal(): float
    {
        $qty = (float)($this->quantity ?? 0);
        $rate = (float)($this->unit_price ?? 0);
        if ($this->quantity === null && $this->unit_price !== null) {
            // treat unit_price as lump sum when quantity is null
            return round($rate, 2);
        }
        return round($qty * $rate, 2);
    }
}
