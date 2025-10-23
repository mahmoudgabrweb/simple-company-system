<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Quotation;
use App\Models\Variation;
use App\Models\Employee;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MilestonesController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(Milestone::class));

        $companyId = $this->activeCompanyId();

        $q = Milestone::with(['milestonable'])
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->orderByDesc('due_date')->orderByDesc('id');

        // Filters
        if ($request->filled('type')) {
            // 'quotation' or 'variation' → map to model FQCN
            $type = $request->type;
            $map = [
                'quotation' => Quotation::class,
                'variation' => Variation::class,
            ];
            if (isset($map[$type])) $q->where('milestonable_type', $map[$type]);
        }

        if ($request->filled('parent_id')) {
            $q->where('milestonable_id', $request->integer('parent_id'));
        }

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($request->filled('date_from')) $q->where('due_date', '>=', $request->date('date_from')->format('Y-m-d'));
        if ($request->filled('date_to')) $q->where('due_date', '<=', $request->date('date_to')->format('Y-m-d'));

        // Totals (filtered)
        $sumAll = (clone $q)->reorder()->sum('amount');

        $milestones = $q->paginate(20)->appends($request->query());

        // Dropdown data (company-scoped lists)
        $quotations = Quotation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'quotation_number']);
        $variations = Variation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'title']);

        $statuses = ['planned' => 'Planned', 'approved' => 'Approved', 'invoiced' => 'Invoiced', 'paid' => 'Paid', 'cancelled' => 'Cancelled'];

        return view('admin.milestones.index', compact('milestones', 'quotations', 'variations', 'statuses', 'sumAll'));
    }

    public function create()
    {
        $this->authorize('add', app(Milestone::class));

        $companyId = $this->activeCompanyId();

        $quotations = Quotation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'quotation_number']);
        $variations = Variation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'title']);
        $statuses = ['planned' => 'Planned', 'approved' => 'Approved', 'invoiced' => 'Invoiced', 'paid' => 'Paid', 'cancelled' => 'Cancelled'];

        if (!$companyId) session()->flash('error', 'No active company — displaying all quotations and variations.');

        return view('admin.milestones.create', compact('quotations', 'variations', 'statuses'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(Milestone::class));

        $companyId = $this->activeCompanyId();
        if (!$companyId) return back()->with('error', 'No active company.')->withInput();

        $data = $request->validate([
            'type' => ['required', 'in:quotation,variation'],
            'parent_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,approved,invoiced,paid,cancelled'],
            'order_index' => ['nullable', 'integer', 'min:1'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        // Map type -> model
        $map = ['quotation' => Quotation::class, 'variation' => Variation::class];
        $parentModel = $map[$data['type']];
        $parent = $parentModel::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($data['parent_id']);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('milestones', 'public');
        }

        Milestone::create([
            'company_id' => $companyId,
            'milestonable_type' => $parentModel,
            'milestonable_id' => $parent->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'due_date' => $data['due_date'] ?? null,
            'status' => $data['status'],
            'order_index' => $data['order_index'] ?? 1,
            'attachment_path' => $path,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.milestones.index')->with('success', 'Milestone created.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(Milestone::class));

        $companyId = $this->activeCompanyId();
        $milestone = Milestone::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with('milestonable')->findOrFail($id);

        $quotations = Quotation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'quotation_number']);
        $variations = Variation::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderByDesc('id')->get(['id', 'title']);
        $statuses = ['planned' => 'Planned', 'approved' => 'Approved', 'invoiced' => 'Invoiced', 'paid' => 'Paid', 'cancelled' => 'Cancelled'];

        // Derive current type/parent for the form
        $currentType = $milestone->milestonable_type === Quotation::class ? 'quotation' : 'variation';
        $currentParentId = $milestone->milestonable_id;

        return view('admin.milestones.edit', compact('milestone', 'quotations', 'variations', 'statuses', 'currentType', 'currentParentId'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(Milestone::class));

        $companyId = $this->activeCompanyId();
        $milestone = Milestone::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        $data = $request->validate([
            'type' => ['required', 'in:quotation,variation'],
            'parent_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,approved,invoiced,paid,cancelled'],
            'order_index' => ['nullable', 'integer', 'min:1'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $map = ['quotation' => Quotation::class, 'variation' => Variation::class];
        $parentModel = $map[$data['type']];
        $parent = $parentModel::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($data['parent_id']);

        if ($request->hasFile('attachment')) {
            if ($milestone->attachment_path) Storage::disk('public')->delete($milestone->attachment_path);
            $milestone->attachment_path = $request->file('attachment')->store('milestones', 'public');
        }

        $milestone->fill([
            'milestonable_type' => $parentModel,
            'milestonable_id' => $parent->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'due_date' => $data['due_date'] ?? null,
            'status' => $data['status'],
            'order_index' => $data['order_index'] ?? $milestone->order_index,
            'updated_by' => auth()->id(),
        ])->save();

        return redirect()->route('voyager.milestones.index')->with('success', 'Milestone updated.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(Milestone::class));

        $companyId = $this->activeCompanyId();
        $milestone = Milestone::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if ($milestone->attachment_path) Storage::disk('public')->delete($milestone->attachment_path);

        $milestone->delete();
        return back()->with('success', 'Milestone deleted.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(Milestone::class));

        $companyId = $this->activeCompanyId();
        $milestone = Milestone::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$milestone->attachment_path || !Storage::disk('public')->exists($milestone->attachment_path)) abort(404);

        return Storage::disk('public')->download($milestone->attachment_path);
    }
}
