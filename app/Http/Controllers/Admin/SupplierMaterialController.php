<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierMaterial;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\Employee;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierMaterialController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();

        $q = SupplierMaterial::with(['supplier', 'project', 'employee'])
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->orderByDesc('paid_at')->orderByDesc('id');

        // Filters
        if ($request->filled('supplier_id')) $q->where('supplier_id', $request->integer('supplier_id'));
        if ($request->filled('project_id')) $q->where('project_id', $request->integer('project_id'));
        if ($request->filled('date_from')) $q->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        if ($request->filled('date_to')) $q->where('paid_at', '<=', $request->date('date_to')->endOfDay());

        $sumAll = (clone $q)->reorder()->sum('amount');

        $materials = $q->paginate(20)->appends($request->query());
        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();

        return view('admin.supplier_materials.index', compact('materials', 'suppliers', 'projects', 'sumAll'));
    }

    public function create()
    {
        $this->authorize('add', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();

        if (!$companyId) session()->flash('error', 'No active company selected — displaying all suppliers, projects and employees.');

        return view('admin.supplier_materials.create', compact('suppliers', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        if (!$companyId) return back()->with('error', 'No active company.')->withInput();

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'paid_at' => ['nullable', 'date'],
            'invoice_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('invoice_attachment')) {
            $path = $request->file('invoice_attachment')->store('supplier-materials', 'public');
        }

        SupplierMaterial::create([
            ...$data,
            'company_id' => $companyId,
            'paid_at' => $data['paid_at'] ?? now(),
            'invoice_attachment_path' => $path,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.supplier_materials.index')->with('success', 'Supplier material added.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        $material = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with(['supplier', 'project', 'employee'])
            ->findOrFail($id);

        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();

        return view('admin.supplier_materials.edit', compact('material', 'suppliers', 'projects', 'employees'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        $material = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'paid_at' => ['nullable', 'date'],
            'invoice_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        if ($request->hasFile('invoice_attachment')) {
            if ($material->invoice_attachment_path)
                Storage::disk('public')->delete($material->invoice_attachment_path);
            $material->invoice_attachment_path = $request->file('invoice_attachment')->store('supplier-materials', 'public');
        }

        $material->fill([
            ...$data,
            'updated_by' => auth()->id(),
        ])->save();

        return redirect()->route('voyager.supplier_materials.index')->with('success', 'Supplier material updated.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        $material = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if ($material->invoice_attachment_path) Storage::disk('public')->delete($material->invoice_attachment_path);

        $material->delete();
        return back()->with('success', 'Supplier material deleted.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(SupplierMaterial::class));

        $companyId = $this->activeCompanyId();
        $material = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$material->invoice_attachment_path || !Storage::disk('public')->exists($material->invoice_attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($material->invoice_attachment_path);
    }
}
