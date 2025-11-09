<?php


namespace App\Models;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'icon',
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
                ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Boot & Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::creating(function (Service $model) {
            // Company & audit defaults
            $model->company_id ??= CompanyContext::id();
            $model->created_by ??= auth()->id();

            // Slug & ordering defaults
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = static::uniqueSlug($model->title, $model->company_id);
            }

            $model->display_order ??= 0;
        });

        static::updating(function (Service $model) {
            $model->updated_by = auth()->id();

            // If slug is empty but title exists (rare), rebuild it
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = static::uniqueSlug($model->title, $model->company_id, $model->id);
            }
        });

        // If title changes and slug not manually set, refresh slug (optional safety)
        static::saving(function (Service $model) {
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
                $model->slug = static::uniqueSlug($model->title, $model->company_id, $model->id);
            }
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
        // Note: a DB unique index on slug already exists; if you later need
        // slug uniqueness per company instead of global, drop the unique()
        // in migration and replace with ->unique(['company_id','slug']).
    }
}
