<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteProject extends Model
{
    protected $fillable = [
        'name',
        'image',
        'short_description',
        'full_description',
        'is_active',
        'client',
        'duration',
        'category',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function achievements(): HasMany
    {
        return $this->hasMany(SiteProjectAchievement::class);
    }

    public function sliders(): HasMany
    {
        return $this->hasMany(SiteProjectSlider::class);
    }
}
