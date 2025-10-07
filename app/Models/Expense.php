<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCompany;

class Expense extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id','expense_type_id',
        'title','amount','description','spent_at',
        'created_by','updated_by','deleted_by',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'spent_at' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

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

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted(): void
    {
        static::creating(function ($m) {
            if (auth()->check()) $m->created_by = $m->created_by ?: auth()->id();
        });

        static::updating(function ($m) {
            if (auth()->check()) $m->updated_by = auth()->id();
        });

        // Ensure deleted_by is saved on soft delete
        static::deleting(function ($m) {
            if (!$m->isForceDeleting() && auth()->check()) {
                $m->deleted_by = auth()->id();
                // Avoid recursion: persist attribute without firing events
                $m->timestamps = false;
                $m->saveQuietly();
                $m->timestamps = true;
            }
        });
    }
}
