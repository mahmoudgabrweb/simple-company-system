<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuotationSent;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = \App\Models\Quotation::with('project')->latest()->paginate(15);
        return view('admin.quotations.index', compact('quotations'));
    }

    public function create() {
        $projects = \App\Models\Project::orderBy('name')->get();
        return view('admin.quotations.create', compact('projects'));
    }

    // Create a new quotation for a project + inject static items
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'code' => ['nullable', 'string', 'max:190'],
            'version' => ['nullable', 'integer', 'min:1'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        return DB::transaction(function () use ($data, $project) {
            // If activating, ensure only one active per project
            $isActive = (bool)($data['is_active'] ?? false);
            if ($isActive) {
                Quotation::where('project_id', $project->id)->update(['is_active' => false]);
            }

            $q = Quotation::create([
                'project_id' => $project->id,
                'code' => $data['code'] ?? null,
                'version' => $data['version'] ?? ($project->quotations()->max('version') + 1) ?? 1,
                'status' => Quotation::ST_DRAFT,
                'is_active' => $isActive,
                'vat_rate' => $data['vat_rate'] ?? 0,
            ]);

            // Inject static items (Mobilization, Demobilization) as priceable lines (lump by default = 0)
            $order = 1;
            QuotationItem::create([
                'quotation_id' => $q->id,
                'title' => 'Mobilization',
                'node_type' => 'line',
                'is_static' => true,
                'pricing_kind' => 'lump',
                'lump_sum' => 0,
                'include_in_total' => true,
                'order' => $order++,
            ]);

            QuotationItem::create([
                'quotation_id' => $q->id,
                'title' => 'Demobilization',
                'node_type' => 'line',
                'is_static' => true,
                'pricing_kind' => 'lump',
                'lump_sum' => 0,
                'include_in_total' => true,
                'order' => $order++,
            ]);

            return response()->json(['status' => true, 'id' => $q->id]);
        });
    }

    public function edit(int $id)
    {
        $quotation = \App\Models\Quotation::with('project')->findOrFail($id);
        $items = $quotation->items()->with('unit')->orderBy('order')->get();
        $units = \App\Models\Unit::where('is_active',1)->orderBy('code')->get();

        return view('admin.quotations.edit', compact('quotation','items','units'));
    }

    // Update quotation header (status changes trigger PDF/email on sending_to_client)
    public function update(Request $request, int $id)
    {
        $q = Quotation::with('project.client')->findOrFail($id);

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:190'],
            'version' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:draft,sending_to_client,waiting_client_response,client_approved,in_progress,completed'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'issue_date' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($q, $data) {
            // enforce single active per project
            if (isset($data['is_active']) && $data['is_active']) {
                Quotation::where('project_id', $q->project_id)->update(['is_active' => false]);
            }

            $oldStatus = $q->status;
            $q->update($data);

            // Recalc totals with project-level discount
            $this->recalcTotals($q->id);

            // Status-driven actions
            if (($data['status'] ?? null) === Quotation::ST_SENDING) {
                $this->generateAndSend($q->fresh('project.client'));
            }

            if (($data['status'] ?? null) === Quotation::ST_COMPLETED && !$q->completed_at) {
                $q->update(['completed_at' => now()]);
            }

            return response()->json(['status' => true]);
        });
    }

    // Manual resend button
    public function resend(int $id)
    {
        $q = Quotation::with('project.client')->findOrFail($id);
        $this->generateAndSend($q);
        return response()->json(['status' => true]);
    }

    protected function generateAndSend(Quotation $q): void
    {
        // 1) Generate PDF to storage (stub view: 'pdf.quotation')
        $pdfPath = "quotations/QT-{$q->id}.pdf";

        // If using barryvdh/laravel-dompdf:
        // $pdf = \PDF::loadView('pdf.quotation', ['quotation' => $q]);
        // Storage::disk('public')->put($pdfPath, $pdf->output());

        // Temporary placeholder file to avoid dependency errors:
        Storage::disk('public')->put($pdfPath, "Quotation #{$q->id} PDF placeholder generated at " . now());

        $q->update([
            'pdf_path' => $pdfPath,
            'sent_at' => now(),
        ]);

        // 2) Email to client
        $client = $q->project->client;
        if ($client && $client->email) {
            Mail::to($client->email)->send(new QuotationSent($q));
        }
    }

    // Totals = (sum lines included) - project discount% + VAT
    public function recalcTotals(int $quotationId): void
    {
        $q = Quotation::with('project')->findOrFail($quotationId);

        $sum = (float)\App\Models\QuotationItem::where('quotation_id', $q->id)
            ->where('include_in_total', true)
            ->sum('line_total');

        $discountPct = (float)($q->project->discount_percent ?? 0);
        $discAmount = round($sum * ($discountPct / 100), 2);
        $afterDisc = max(0, round($sum - $discAmount, 2));

        $tax = round($afterDisc * ((float)$q->vat_rate / 100), 2);
        $grand = round($afterDisc + $tax, 2);

        $q->updateQuietly([
            'subtotal' => $afterDisc,
            'discount_amount' => $discAmount,
            'tax_total' => $tax,
            'grand_total' => $grand,
        ]);
    }
}
