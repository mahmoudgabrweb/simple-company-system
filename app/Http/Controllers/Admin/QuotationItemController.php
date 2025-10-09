<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationItemController extends Controller
{
    // Create item or sub-item
    public function store(Request $request, int $quotationId)
    {
        $q = Quotation::findOrFail($quotationId);

        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:quotation_items,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:190'],
            'node_type' => ['required', 'in:section,line'],
            'notes' => ['nullable', 'string'],
            'pricing_kind' => ['required', 'in:none,unit,lump'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'lump_sum' => ['nullable', 'numeric', 'min:0'],
            'item_status' => ['nullable', 'in:included,optional,tbd,excluded'],
            'include_in_total' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($q, $data) {
            $order = $data['order'] ?? (int)QuotationItem::where('quotation_id', $q->id)
                ->where('parent_id', $data['parent_id'] ?? null)->max('order') + 1;

            $item = new QuotationItem(array_merge($data, [
                'quotation_id' => $q->id,
                'order' => $order,
            ]));

            $item->line_total = $this->calcLine($item);
            $item->save();

            app(QuotationController::class)->recalcTotals($q->id);

            return response()->json(['status' => true, 'id' => $item->id]);
        });
    }

    public function update(Request $request, int $itemId)
    {
        $item = QuotationItem::findOrFail($itemId);
        $q = $item->quotation;

        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:quotation_items,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:190'],
            'node_type' => ['required', 'in:section,line'],
            'notes' => ['nullable', 'string'],
            'pricing_kind' => ['required', 'in:none,unit,lump'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'lump_sum' => ['nullable', 'numeric', 'min:0'],
            'item_status' => ['nullable', 'in:included,optional,tbd,excluded'],
            'include_in_total' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($item, $q, $data) {
            $item->fill($data);
            $item->line_total = $this->calcLine($item);
            $item->save();

            app(QuotationController::class)->recalcTotals($q->id);

            return response()->json(['status' => true]);
        });
    }

    public function destroy(int $itemId)
    {
        $item = QuotationItem::findOrFail($itemId);
        $q = $item->quotation;
        $item->delete();
        app(QuotationController::class)->recalcTotals($q->id);
        return response()->json(['status' => true]);
    }

    protected function calcLine(QuotationItem $it): float
    {
        if ($it->node_type === 'section') return 0.0;
        if ($it->pricing_kind === 'unit') {
            $qty = (float)($it->quantity ?? 0);
            $rate = (float)($it->unit_price ?? 0);
            return round($qty * $rate, 2);
        }
        if ($it->pricing_kind === 'lump') {
            return round((float)($it->lump_sum ?? 0), 2);
        }
        return 0.0; // none
    }
}
