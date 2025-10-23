<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Supplier;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(Supplier::class));

        $companyId = $this->activeCompanyId();

        $q = Supplier::with('city')
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->orderByDesc('id');

        // Filters
        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->string('name') . '%');
        }

        if ($request->filled('city_id')) {
            $q->where('city_id', $request->integer('city_id'));
        }

        $suppliers = $q->paginate(20)->appends($request->query());
        $cities = City::orderBy('name')->get();

        return view('admin.suppliers.index', compact('suppliers', 'cities'));
    }

    public function create()
    {
        $this->authorize('add', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        $cities = City::where("company_id", CompanyContext::id())->orderBy('name')->get();

        if (!$companyId) {
            session()->flash('error', 'No active company selected — displaying all cities.');
        }

        return view('admin.suppliers.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        if (!$companyId) return back()->with('error', 'No active company.')->withInput();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['required', 'string', 'max:190'],
            'contact_person_phone' => ['required', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:190'],
            'iban' => ['nullable', 'string', 'max:190'],
            'swift' => ['nullable', 'string', 'max:190'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('suppliers', 'public');
        }

        Supplier::create([
            ...$data,
            'company_id' => $companyId,
            'attachment_path' => $path,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        $supplier = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with('city')
            ->findOrFail($id);

        $cities = City::where("company_id", CompanyContext::id())->orderBy('name')->get();

        return view('admin.suppliers.edit', compact('supplier', 'cities'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        $supplier = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['required', 'string', 'max:190'],
            'contact_person_phone' => ['required', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:190'],
            'iban' => ['nullable', 'string', 'max:190'],
            'swift' => ['nullable', 'string', 'max:190'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($supplier->attachment_path) Storage::disk('public')->delete($supplier->attachment_path);
            $supplier->attachment_path = $request->file('attachment')->store('suppliers', 'public');
        }

        $supplier->fill([
            ...$data,
            'updated_by' => auth()->id(),
        ])->save();

        return redirect()->route('voyager.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        $supplier = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if ($supplier->attachment_path) Storage::disk('public')->delete($supplier->attachment_path);

        $supplier->delete();
        return back()->with('success', 'Supplier deleted.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(Supplier::class));

        $companyId = $this->activeCompanyId();
        $supplier = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$supplier->attachment_path || !Storage::disk('public')->exists($supplier->attachment_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($supplier->attachment_path);
    }
}
