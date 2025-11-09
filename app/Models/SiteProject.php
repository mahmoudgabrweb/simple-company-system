<?php


namespace App\Models;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SiteProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'client',
        'duration',
        'category',
        'cover_image',
        'display_order',
        'is_featured',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
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

    public function images()
    {
        return $this->hasMany(SiteProjectImage::class)->orderBy('display_order');
    }

    public function features()
    {
        return $this->hasMany(SiteProjectFeature::class)->orderBy('display_order');
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderByDesc('id');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%")
                ->orWhere('client', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Boot events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::creating(function (SiteProject $model) {
            $model->company_id ??= CompanyContext::id();
            $model->created_by ??= auth()->id();

            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = static::uniqueSlug($model->title, $model->company_id);
            }
        });

        static::updating(function (SiteProject $model) {
            $model->updated_by = auth()->id();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    protected static function uniqueSlug(string $title, ?int $companyId, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        $query = static::query()->when($companyId, fn($q) => $q->where('company_id', $companyId));
        while (
        $query->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
