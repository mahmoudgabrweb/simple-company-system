<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteService extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'short_description',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];
}
