<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMaterial extends Model
{
    protected $fillable = [
        'company_id',
        'supplier_id',
        'project_id',
        'title',
        'description',
        'amount',
        'paid_by_employee_id',
        'paid_at',
        'invoice_attachment_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function project()
    {
        return $this->belongsTo(SiteProject::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'paid_by_employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
