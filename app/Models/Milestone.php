<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'amount',
        'due_date',
        'status',
        'order_index',
        'attachment_path',
        'milestonable_type',
        'milestonable_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function milestonable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
