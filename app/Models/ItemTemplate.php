<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTemplate extends Model
{
    protected $fillable = [
        'company_id', 'section_template_id', 'name', 'description', 'item_type',
        'default_quantity', 'default_unit_id', 'default_unit_price', 'metadata',
        'created_by', 'usage_count', 'is_active'
    ];
    protected $casts = ['metadata' => 'array', 'is_active' => 'boolean'];
}
