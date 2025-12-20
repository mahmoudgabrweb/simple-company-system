<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteProjectSlider extends Model
{
    protected $fillable = [
        'site_project_id',
        'image',
        'caption',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(SiteProject::class, 'site_project_id');
    }
}
