<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class Project extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'client_id', 'city_id',
        'name', 'address', 'location', 'map',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function activeQuotation()
    {
        return $this->hasOne(Quotation::class)->where('is_active', true);
    }
}