<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationSection extends Model
{
    protected $fillable = ['variation_id', 'title', 'order'];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function items()
    {
        return $this->hasMany(VariationItem::class)->orderBy('order');
    }
}
