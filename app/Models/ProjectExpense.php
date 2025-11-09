<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'project_id', 'title', 'description', 'attachment_path',
        'paid_by_employee_id', 'payment_type', 'amount', 'paid_at',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(SiteProject::class);
    }

    public function payer()
    {
        return $this->belongsTo(Employee::class, 'paid_by_employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
