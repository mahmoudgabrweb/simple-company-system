<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'city_id',
        'address',
        'contact_person_name',
        'contact_person_phone',
        'bank_name',
        'iban',
        'swift',
        'status',
        'notes',
        'attachment_path',
        'created_by',
        'updated_by',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
