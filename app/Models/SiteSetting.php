<?php


namespace App\Models;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'emails',
        'phones',
        'address_line',
        'city',
        'country',
        'whatsapp_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'portfolio_cta_text',
        'portfolio_cta_url',
        'footer_about_title',
        'footer_about_text',
        'logo_path',
        'favicon_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'emails' => 'array',
        'phones' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeForCompany($query, ?int $companyId = null)
    {
        return $query->where('company_id', $companyId ?? CompanyContext::id());
    }

    /*
    |--------------------------------------------------------------------------
    | Boot & helpers
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::creating(function (SiteSetting $model) {
            $model->company_id ??= CompanyContext::id();
            $model->created_by ??= auth()->id();
        });

        static::updating(function (SiteSetting $model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Retrieve or create the singleton settings row for the current (or given) company.
     */
    public static function singleton(?int $companyId = null): self
    {
        $companyId = $companyId ?? CompanyContext::id();

        return static::firstOrCreate(
            ['company_id' => $companyId],
            [
                'emails' => [],
                'phones' => [],
                'portfolio_cta_text' => 'Get Our Portfolio',
                'portfolio_cta_url' => null,
            ]
        );
    }
}
