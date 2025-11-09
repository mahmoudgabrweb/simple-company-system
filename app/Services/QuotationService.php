<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationSection;
use App\Models\QuotationItem;
use App\Models\SiteProject;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    /**
     * Create a new quotation with default static sections
     */
    public function createQuotation(SiteProject $project, array $data = [])
    {
        return DB::transaction(function () use ($project, $data) {
            $quotation = Quotation::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'quotation_number' => $this->generateQuotationNumber($project),
                'version' => 1,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'valid_until' => $data['valid_until'] ?? now()->addDays(30),
                'created_by' => auth()->id(),
            ]);

            // Create static sections A & B
            $this->createStaticSections($quotation);

            return $quotation;
        });
    }

    /**
     * Generate unique quotation number
     */
    private function generateQuotationNumber(SiteProject $project): string
    {
        $count = Quotation::where('project_id', $project->id)->count();
        $projectCode = strtoupper(substr($project->name, 0, 3));
        $year = date('Y');

        return "{$projectCode}-{$year}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create default static sections (A: Preliminaries, B: Mobilization)
     */
    private function createStaticSections(Quotation $quotation)
    {
        // Section A: PRELIMINARIES
        $sectionA = QuotationSection::create([
            'quotation_id' => $quotation->id,
            'letter_code' => 'A',
            'title' => 'PRELIMINARIES',
            'description' => null,
            'is_static' => true,
            'order' => 1,
        ]);

        QuotationItem::create([
            'quotation_section_id' => $sectionA->id,
            'item_number' => 1,
            'title' => null,
            'description' => 'Preparation of all approval documents; all admin works related to authorities application process and the shop drawings.',
            'item_type' => 'simple',
            'quantity' => null,
            'unit' => null,
            'unit_price' => null,
            'total_price' => 4000.00,
            'order' => 1,
        ]);

        $sectionA->calculateTotal();

        // Section B: MOBILIZATION/DEMOBILIZATION
        $sectionB = QuotationSection::create([
            'quotation_id' => $quotation->id,
            'letter_code' => 'B',
            'title' => 'MOBILIZATION/DEMOBILIZATION',
            'description' => null,
            'is_static' => true,
            'order' => 2,
        ]);

        QuotationItem::create([
            'quotation_section_id' => $sectionB->id,
            'item_number' => 1,
            'title' => 'Mobilization/demobilization of the site:',
            'description' => 'Removal and disposal of construction debris, old landscaping, leftover building materials, hauling debris to a landfill or recycling facility.<br>Site survey and leveling the ground.',
            'item_type' => 'simple',
            'quantity' => null,
            'unit' => null,
            'unit_price' => null,
            'total_price' => 14000.00,
            'order' => 1,
        ]);

        $sectionB->calculateTotal();

        $quotation->calculateTotal();
    }

    /**
     * Add new dynamic section to quotation
     */
    public function addSection(Quotation $quotation, string $title, ?string $description = null)
    {
        $nextOrder = $quotation->sections()->max('order') + 1;
        $letterCode = QuotationSection::getNextLetterCode($quotation);

        return QuotationSection::create([
            'quotation_id' => $quotation->id,
            'letter_code' => $letterCode,
            'title' => $title,
            'description' => $description,
            'is_static' => false,
            'order' => $nextOrder,
        ]);
    }

    /**
     * Reorder sections (keeping A & B at top)
     */
    public function reorderSections(Quotation $quotation, array $sectionIds)
    {
        DB::transaction(function () use ($quotation, $sectionIds) {
            $order = 3; // Start after A & B

            foreach ($sectionIds as $sectionId) {
                $section = QuotationSection::find($sectionId);
                if ($section && !$section->is_static) {
                    $section->update(['order' => $order]);
                    $order++;
                }
            }
        });
    }

    /**
     * Add item to section
     */
    public function addItem(QuotationSection $section, array $data)
    {
        $nextOrder = $section->items()->max('order') + 1;
        $nextItemNumber = $section->items()->max('item_number') + 1;

        $item = QuotationItem::create([
            'quotation_section_id' => $section->id,
            'parent_item_id' => $data['parent_item_id'] ?? null,
            'item_number' => $nextItemNumber,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'item_type' => $data['item_type'] ?? 'simple',
            'quantity' => $data['quantity'] ?? null,
            'unit' => $data['unit'] ?? null,
            'unit_price' => $data['unit_price'] ?? null,
            'total_price' => $data['total_price'] ?? 0,
            'metadata' => $data['metadata'] ?? null,
            'order' => $nextOrder,
            'is_excluded' => $data['is_excluded'] ?? false,
        ]);

        $item->calculateTotal();

        return $item;
    }

    /**
     * Update item
     */
    public function updateItem(QuotationItem $item, array $data)
    {
        $item->update([
            'title' => $data['title'] ?? $item->title,
            'description' => $data['description'] ?? $item->description,
            'item_type' => $data['item_type'] ?? $item->item_type,
            'quantity' => $data['quantity'] ?? $item->quantity,
            'unit' => $data['unit'] ?? $item->unit,
            'unit_price' => $data['unit_price'] ?? $item->unit_price,
            'total_price' => $data['total_price'] ?? $item->total_price,
            'metadata' => $data['metadata'] ?? $item->metadata,
            'is_excluded' => $data['is_excluded'] ?? $item->is_excluded,
        ]);

        $item->calculateTotal();

        return $item;
    }

    /**
     * Delete item
     */
    public function deleteItem(QuotationItem $item)
    {
        $section = $item->section;
        $item->delete();
        $section->calculateTotal();
    }

    /**
     * Clone section within same quotation or to another
     */
    public function cloneSection(QuotationSection $section, Quotation $targetQuotation = null)
    {
        $targetQuotation = $targetQuotation ?? $section->quotation;

        return DB::transaction(function () use ($section, $targetQuotation) {
            return $section->duplicateToQuotation($targetQuotation);
        });
    }

    /**
     * Clone entire quotation
     */
    public function cloneQuotation(Quotation $quotation)
    {
        return DB::transaction(function () use ($quotation) {
            return $quotation->duplicate();
        });
    }

    /**
     * Activate quotation (make it the active one for the project)
     */
    public function activateQuotation(Quotation $quotation)
    {
        DB::transaction(function () use ($quotation) {
            $quotation->activate();
        });
    }

    /**
     * Approve quotation
     */
    public function approveQuotation(Quotation $quotation)
    {
        DB::transaction(function () use ($quotation) {
            $quotation->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Get quotation statistics
     */
    public function getQuotationStats(Quotation $quotation)
    {
        return [
            'total_sections' => $quotation->sections()->count(),
            'dynamic_sections' => $quotation->sections()->where('is_static', false)->count(),
            'total_items' => QuotationItem::whereHas('section', function ($q) use ($quotation) {
                $q->where('quotation_id', $quotation->id);
            })->count(),
            'total_amount' => $quotation->total_amount,
        ];
    }
}