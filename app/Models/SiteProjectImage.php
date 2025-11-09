<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_project_id',
        'image_path',
        'caption',
        'display_order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function project()
    {
        return $this->belongsTo(SiteProject::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }
}
