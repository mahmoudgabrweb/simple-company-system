<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItemDetail extends Model
{
    protected $fillable = ['quotation_item_id', 'detail_type', 'label', 'value', 'extra_data', 'order'];
    protected $casts = ['extra_data' => 'array'];

    public function item()
    {
        return $this->belongsTo(QuotationItem::class, 'quotation_item_id');
    }
}
