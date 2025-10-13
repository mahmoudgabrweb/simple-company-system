<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'quotation_id', 'code', 'title', 'notes', 'status', 'currency', 'valid_until',
        'subtotal', 'tax', 'total',
    ];

    protected $casts = [
        'valid_until' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function sections()
    {
        return $this->hasMany(VariationSection::class)->orderBy('order');
    }

    // compute totals from sections->items
    public function refreshTotals(): void
    {
        $subtotal = 0;
        foreach ($this->sections()->with('items')->get() as $sec) {
            foreach ($sec->items as $it) {
                $subtotal += (float)$it->subtotal;
            }
        }
        $this->subtotal = $subtotal;
        // simple tax pass-through; adapt to your tax rules if needed
        $this->tax = 0;
        $this->total = $this->subtotal + $this->tax;
        $this->save();
    }

    public function scopeForProject($q, $projectId)
    {
        return $q->where('project_id', $projectId);
    }
}
