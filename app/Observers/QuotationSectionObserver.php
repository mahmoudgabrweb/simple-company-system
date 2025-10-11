<?php

namespace App\Observers;

use App\Models\QuotationSection;

class QuotationSectionObserver
{
    public function saved(QuotationSection $section): void
    {
        // keep section total + quotation total consistent
        $section->recomputeTotals();
    }

    public function deleted(QuotationSection $section): void
    {
        if ($section->quotation) {
            $section->quotation->recomputeTotals();
        }
    }
}
