<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteClient extends Model
{
    protected $fillable = ["name", "image", "display_order", "is_active"];
}
