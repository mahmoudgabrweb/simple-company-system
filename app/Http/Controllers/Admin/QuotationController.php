<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuotationSent;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationSection;
use App\Models\Unit;
use App\Models\Project;
use App\Support\CompanyContext;
use Barryvdh\DomPDF\Facade\Pdf; // at top
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    // List
    public function index()
    {
        $this->authorize('browse', app(Quotation::class)); // Voyager policy check (optional)
        $quotations = Quotation::with('project')->latest()->paginate(20);
        return view('admin.quotations.index', compact('quotations'));
    }

    // Create form
    public function create()
    {
        $this->authorize('add', app(Quotation::class));
        $projects = Project::orderBy('name')->get();
        return view('admin.quotations.create', compact('projects'))
            ->with('currentCompany', CompanyContext::company());
    }

    private function active_company_id(): ?int
    {
        // whichever you used in Projects:
        // return session('active_company_id');
        // or return auth()->user()->company_id;
        // or return app('company')->id;
        return session('active_company_id');
    }

    // Create quotation (auto-inject static sections is handled by observer)
    public function store(Request $request)
    {
        $this->authorize('add', app(Quotation::class));

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'quotation_number' => ['nullable', 'string', 'max:190'],
            'version' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $cid = CompanyContext::id();

        // force consistency for "one active per project" via observer
        $q = Quotation::create([
            "company_id" => $cid,
            'project_id' => $data['project_id'],
            'quotation_number' => $data['quotation_number'] ?? null,
            'version' => $data['version'] ?? 1,
            'is_active' => (bool)($data['is_active'] ?? false),
            'status' => 'draft',
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.quotations.edit', $q->id)->with('success', 'تم إنشاء العرض وإضافة الأقسام الثابتة.');
    }

    // Edit/builder
    public function edit(int $id)
    {
        $this->authorize('edit', app(Quotation::class));
        $quotation = Quotation::with(['project', 'sections.allItems.unit'])->findOrFail($id);
        $units = Unit::where('is_active', 1)->orderBy('code')->get();
        return view('admin.quotations.edit', compact('quotation', 'units'));
    }

    // Update quotation header (status, is_active, notes, etc.)
    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(Quotation::class));
        $q = Quotation::findOrFail($id);

        $data = $request->validate([
            'quotation_number' => ['nullable', 'string', 'max:190'],
            'version' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:draft,sent,waiting_client_response,approved,rejected,in_progress,completed'],
            'notes' => ['nullable', 'string'],
            'valid_until' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($q, $data) {
            $q->fill($data);
            // observers handle single-active when is_active changes
            $q->save();
        });

        return back()->with('success', 'تم حفظ البيانات.');
    }

    // Destroy quotation
    public function destroy(int $id)
    {
        $this->authorize('delete', app(Quotation::class));
        $q = Quotation::findOrFail($id);
        $q->delete();
        return redirect()->route('voyager.quotations.index')->with('success', 'تم حذف العرض.');
    }

    // Change status (lightweight)
    public function changeStatus(Request $request, int $id)
    {
        $this->authorize('edit', app(Quotation::class));
        $q = Quotation::findOrFail($id);
        $data = $request->validate([
            'status' => ['required', 'in:draft,sent,waiting_client_response,approved,rejected,in_progress,completed']
        ]);
        $q->status = $data['status'];
        if ($q->status === 'approved' && !$q->approved_at) {
            $q->approved_at = now();
            $q->approved_by = auth()->id();
        }
        $q->save();
        return back()->with('success', 'تم تغيير الحالة.');
    }

    // Send & Resend (generate PDF + email)
    public function send(int $id)
    {
        return $this->doSend($id, true);
    }

    public function resend(int $id)
    {
        return $this->doSend($id, false);
    }

    protected function doSend(int $id, bool $markSent)
    {
        $this->authorize('edit', app(\App\Models\Quotation::class));

        $q = \App\Models\Quotation::with([
            'project.client',
            'sections' => function($q){ $q->orderBy('order'); },
            'sections.allItems' => function($q){ $q->orderBy('order'); },
            'sections.allItems.unit',
        ])->findOrFail($id);

        // generate PDF
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $q])
            ->setPaper('a4', 'portrait');

        $path = "quotations/QT-{$q->id}.pdf";
        \Storage::disk('public')->put($path, $pdf->output());
        $q->pdf_path = $path;

        if ($markSent) {
            $q->status = 'sent';
            $q->sent_at = now();
        }
        $q->save();

        // email (attach PDF)
        if (optional($q->project->client)->email) {
            \Mail::to($q->project->client->email)->send(new \App\Mail\QuotationSent($q));
            // If you want the PDF attached from storage inside the Mailable,
            // open QuotationSent and call ->attachFromStorageDisk('public', $q->pdf_path, 'quotation.pdf')
        }

        return back()->with('success','تم إنشاء PDF وإرساله للعميل.');
    }

    /* ========================= Sections ========================= */

    public function addSection(Request $request, int $quotationId)
    {
        $this->authorize('edit', app(Quotation::class));
        $q = Quotation::findOrFail($quotationId);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
        ]);

        $order = (int)QuotationSection::where('quotation_id', $q->id)->max('order') + 1;

        $section = QuotationSection::create([
            'quotation_id' => $q->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_static' => false,
            'order' => $order,
        ]);

        return back()->with('success', 'تم إضافة قسم.');
    }

    public function updateSection(Request $request, int $sectionId)
    {
        $this->authorize('edit', app(Quotation::class));
        $s = QuotationSection::findOrFail($sectionId);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
        ]);
        $s->update($data);
        return back()->with('success', 'تم حفظ القسم.');
    }

    public function deleteSection(int $sectionId)
    {
        $this->authorize('delete', app(Quotation::class));
        $s = QuotationSection::findOrFail($sectionId);
        if ($s->is_static) {
            return back()->with('error', 'لا يمكن حذف الأقسام الثابتة.');
        }
        $s->delete();
        return back()->with('success', 'تم حذف القسم.');
    }

    public function reorderSections(Request $request, int $quotationId)
    {
        $this->authorize('edit', app(Quotation::class));
        $q = Quotation::findOrFail($quotationId);
        $data = $request->validate([
            'orders' => ['required', 'array'], // [section_id => order, ...]
        ]);
        DB::transaction(function () use ($data, $q) {
            foreach ($data['orders'] as $id => $order) {
                QuotationSection::where('quotation_id', $q->id)->where('id', $id)->update(['order' => (int)$order]);
            }
        });
        return response()->json(['status' => true]);
    }

    /* ========================= Items ========================= */

    public function addItem(Request $request, int $sectionId)
    {
        $this->authorize('edit', app(Quotation::class));
        $section = QuotationSection::findOrFail($sectionId);

        $data = $request->validate([
            'parent_item_id' => ['nullable', 'exists:quotation_items,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'item_type' => ['nullable', 'string', 'max:40'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'is_excluded' => ['nullable', 'boolean'],
        ]);

        $order = (int)QuotationItem::where('quotation_section_id', $section->id)
                ->where('parent_item_id', $data['parent_item_id'] ?? null)
                ->max('order') + 1;

        $item = new QuotationItem(array_merge($data, [
            'quotation_section_id' => $section->id,
            'order' => $order,
        ]));

        // compute total (observer also does this, but we set once)
        $item->total_price = $item->computeTotal();
        $item->save();

        return back()->with('success', 'تم إضافة عنصر.');
    }

    public function updateItem(Request $request, int $itemId)
    {
        $this->authorize('edit', app(Quotation::class));
        $item = QuotationItem::with('section')->findOrFail($itemId);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'item_type' => ['nullable', 'string', 'max:40'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'is_excluded' => ['nullable', 'boolean'],
        ]);

        $item->fill($data);
        $item->total_price = $item->computeTotal();
        $item->save();

        return back()->with('success', 'تم حفظ العنصر.');
    }

    public function deleteItem(int $itemId)
    {
        $this->authorize('delete', app(Quotation::class));
        $item = QuotationItem::findOrFail($itemId);
        $item->delete();
        return back()->with('success', 'تم حذف العنصر.');
    }

    public function reorderItems(Request $request, int $sectionId)
    {
        $this->authorize('edit', app(Quotation::class));
        $section = QuotationSection::findOrFail($sectionId);
        $data = $request->validate([
            'orders' => ['required', 'array'], // [item_id => order, ...]
        ]);
        DB::transaction(function () use ($data, $section) {
            foreach ($data['orders'] as $id => $order) {
                QuotationItem::where('quotation_section_id', $section->id)->where('id', $id)->update(['order' => (int)$order]);
            }
        });
        return response()->json(['status' => true]);
    }
}
