<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationItem extends Model
{
    protected $fillable = [
        'variation_section_id', 'name', 'description', 'unit_id', 'qty', 'unit_price',
        'discount_type', 'discount_value', 'subtotal', 'order'
    ];

    public function section()
    {
        return $this->belongsTo(VariationSection::class, 'variation_section_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function computeSubtotal(): float
    {
        $raw = (float)$this->qty * (float)$this->unit_price;
        $disc = 0;
        if ($this->discount_type === 'percent') {
            $disc = $raw * (min(100, max(0, (float)$this->discount_value)) / 100);
        } elseif ($this->discount_type === 'fixed') {
            $disc = min($raw, max(0, (float)$this->discount_value));
        }
        return $this->subtotal = round($raw - $disc, 2);
    }
}
