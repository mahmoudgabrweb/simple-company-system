<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Employee extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'job_id', 'name', 'phone', 'email',
        'cv_path', 'image_path', 'start_at', 'end_at', 'salary',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'salary' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return is_null($this->end_at);
    }
}
