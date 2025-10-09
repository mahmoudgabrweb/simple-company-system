<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'code',      // e.g. m, m2, pcs
        'name_ar',   // Arabic label
        'name_en',   // English label
        'kind',      // length | area | volume | count | weight | other
        'is_active',
    ];

    public function quotationItems()
    {
        return $this->hasMany(\App\Models\QuotationItem::class, 'unit_id');
    }
}
