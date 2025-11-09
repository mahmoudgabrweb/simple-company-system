<?php


namespace App\Models;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeHero extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'kicker',
        'headline',
        'subheadline',
        'primary_cta_text',
        'primary_cta_url',
        'secondary_cta_text',
        'secondary_cta_url',
        'background_type',
        'background_value',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeForCompany($q, ?int $companyId = null)
    {
        return $q->where('company_id', $companyId ?? CompanyContext::id());
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // Boot
    protected static function booted(): void
    {
        static::creating(function (HomeHero $m) {
            $m->company_id ??= CompanyContext::id();
            $m->created_by ??= auth()->id();
        });
        static::updating(function (HomeHero $m) {
            $m->updated_by = auth()->id();
        });
    }

    // Singleton helper
    public static function singleton(?int $companyId = null): self
    {
        $companyId = $companyId ?? CompanyContext::id();
        return static::firstOrCreate(
            ['company_id' => $companyId],
            [
                'kicker' => 'inspired interiors',
                'headline' => 'Designing your dream spaces, one room at a time',
                'subheadline' => '',
                'primary_cta_text' => 'Get Our Portfolio',
                'primary_cta_url' => '#portfolio',
                'secondary_cta_text' => null,
                'secondary_cta_url' => null,
                'background_type' => 'image',
                'background_value' => null,
                'is_active' => true,
            ]
        );
    }
}
