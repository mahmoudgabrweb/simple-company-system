<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationPdfController extends Controller
{
    public function download(Quotation $quotation)
    {
        $quotation->load([
            'company',
            'project.client',
            'sections' => fn($q) => $q->orderBy('order'),
            'sections.items' => fn($q) => $q->whereNull('parent_item_id')->orderBy('order'),
            'sections.items.unit',
            'sections.items.details',
            'sections.items.children' => fn($q) => $q->orderBy('order'),
            'sections.items.children.unit',
            'sections.items.children.details',
        ]);

        $branding = [
            'logo' => public_path('pdf-assets/kame-logo.png'),
            'brand' => 'K A M E LANDSCAPING',
            'website' => 'www.kame.ae',
            'doc_title' => 'AGREEMENT',
        ];

        $pdf = Pdf::loadView('admin.pdf.quotation', compact('quotation', 'branding'))
            ->setPaper('A4', 'portrait');

        $fileName = 'Quotation-' . ($quotation->quotation_number ?? $quotation->id) . '.pdf';
        return $pdf->download($fileName);
    }
}
