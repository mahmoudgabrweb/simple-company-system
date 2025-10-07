<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Models\Role;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function pmtPricing(): BelongsToMany
    {
        return $this->belongsToMany(ProductManufacturingType::class, 'user_pmt_pricing');
    }

    public function pmtStore(): BelongsToMany
    {
        return $this->belongsToMany(ProductManufacturingType::class, 'user_pmt_store');
    }

    public function productNameStore(): BelongsToMany
    {
        return $this->belongsToMany(ProductName::class, 'user_product_name_store');
    }

    public function vehicleMakerPricing(): BelongsToMany
    {
        return $this->belongsToMany(VehicleMaker::class, 'user_vehicle_maker_pricing');
    }

    public function vehicleMakerStore(): BelongsToMany
    {
        return $this->belongsToMany(VehicleMaker::class, 'user_vehicle_maker_store');
    }
}
