<?php


namespace App\Models;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AboutBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'body',
        'video_url',
        'created_by',
        'updated_by',
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
        static::creating(function (AboutBlock $model) {
            $model->company_id ??= CompanyContext::id();
            $model->created_by ??= auth()->id();
        });

        static::updating(function (AboutBlock $model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Retrieve or create the singleton About block for the current (or given) company.
     */
    public static function singleton(?int $companyId = null): self
    {
        $companyId = $companyId ?? CompanyContext::id();

        return static::firstOrCreate(
            ['company_id' => $companyId],
            [
                'title' => 'About Us',
                'body' => '',
                'video_url' => null,
            ]
        );
    }
}
