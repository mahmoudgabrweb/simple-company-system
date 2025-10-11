<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTemplate extends Model
{
    protected $fillable = ['company_id', 'name', 'description', 'is_public', 'created_by', 'usage_count', 'is_active'];
    protected $casts = ['is_public' => 'boolean', 'is_active' => 'boolean'];
}
