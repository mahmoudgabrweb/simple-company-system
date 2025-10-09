<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'parent_id', 'code', 'title', 'node_type', 'is_static', 'notes',
        'pricing_kind', 'unit_id', 'quantity', 'unit_price', 'lump_sum',
        'item_status', 'include_in_total', 'line_total', 'order'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function parent()
    {
        return $this->belongsTo(QuotationItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(QuotationItem::class, 'parent_id')->orderBy('order');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
