<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'company_id',
        'supplier_id',
        'supplier_material_id',
        'title',
        'description',
        'amount',
        'payment_type',
        'paid_by_employee_id',
        'paid_at',
        'attachment_path',
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

    public function material()
    {
        return $this->belongsTo(SupplierMaterial::class, 'supplier_material_id');
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
