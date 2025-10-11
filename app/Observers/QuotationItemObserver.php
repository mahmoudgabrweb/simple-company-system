<?php

namespace App\Observers;

use App\Models\QuotationItem;

class QuotationItemObserver
{
    public function creating(QuotationItem $item): void
    {
        // compute total before save
        $item->total_price = $item->computeTotal();
    }

    public function updating(QuotationItem $item): void
    {
        if ($item->isDirty(['quantity', 'unit_price'])) {
            $item->total_price = $item->computeTotal();
        }
    }

    public function saved(QuotationItem $item): void
    {
        // bubble up totals to section and quotation
        $item->section->recomputeTotals();
    }

    public function deleted(QuotationItem $item): void
    {
        if ($item->section) {
            $item->section->recomputeTotals();
        }
    }
}
