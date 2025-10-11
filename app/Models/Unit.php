<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['code', 'name_ar', 'name_en', 'kind', 'is_active'];
}
