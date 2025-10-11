<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationSection;
use Illuminate\Support\Facades\DB;

class QuotationObserver
{
    public function creating(Quotation $q): void
    {
        // Enforce one active per project (if this is set active)
        if ($q->is_active && $q->project_id) {
            Quotation::where('project_id', $q->project_id)->update(['is_active' => false]);
        }
    }

    public function created(Quotation $q): void
    {
        // Auto-inject static sections A & B with one price line each (0 by default)
        DB::transaction(function () use ($q) {
            // A: PRELIMINARIES
            $a = QuotationSection::create([
                'quotation_id' => $q->id,
                'letter_code' => 'A',
                'title' => 'PRELIMINARIES',
                'description' => null,
                'is_static' => true,
                'order' => 1,
            ]);
            QuotationItem::create([
                'quotation_section_id' => $a->id,
                'title' => 'Preliminaries Lump Sum',
                'item_type' => 'simple',
                'quantity' => null,         // null qty = treat unit_price as lump
                'unit_price' => 0,
                'total_price' => 0,
                'order' => 1,
                'is_excluded' => false,
            ]);

            // B: Mobilization / Demobilization
            $b = QuotationSection::create([
                'quotation_id' => $q->id,
                'letter_code' => 'B',
                'title' => 'Mobilization / Demobilization',
                'description' => null,
                'is_static' => true,
                'order' => 2,
            ]);
            QuotationItem::create([
                'quotation_section_id' => $b->id,
                'title' => 'Mobilization & Demobilization Lump Sum',
                'item_type' => 'simple',
                'quantity' => null,
                'unit_price' => 0,
                'total_price' => 0,
                'order' => 1,
                'is_excluded' => false,
            ]);

            $a->recomputeTotals();
            $b->recomputeTotals();
        });
    }

    public function updating(Quotation $q): void
    {
        if ($q->isDirty('is_active') && $q->is_active && $q->project_id) {
            Quotation::where('project_id', $q->project_id)
                ->where('id', '!=', $q->id)
                ->update(['is_active' => false]);
        }
    }
}
