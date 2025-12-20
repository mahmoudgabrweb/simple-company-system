<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\SupplierMaterial;
use App\Models\Project;
use App\Models\Employee;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierPaymentController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();

        $q = SupplierPayment::with(['supplier', 'material', 'employee'])
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->orderByDesc('paid_at')->orderByDesc('id');

        // Filters
        if ($request->filled('supplier_id')) $q->where('supplier_id', $request->integer('supplier_id'));
        if ($request->filled('payment_type')) $q->where('payment_type', $request->string('payment_type'));
        if ($request->filled('date_from')) $q->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        if ($request->filled('date_to')) $q->where('paid_at', '<=', $request->date('date_to')->endOfDay());

        $sumAll = (clone $q)->reorder()->sum('amount');

        $payments = $q->paginate(20)->appends($request->query());
        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        return view('admin.supplier_payments.index', compact('payments', 'suppliers', 'types', 'sumAll'));
    }

    public function create()
    {
        $this->authorize('add', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $materials = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('title')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        if (!$companyId) session()->flash('error', 'No active company — displaying all records.');

        return view('admin.supplier_payments.create', compact('suppliers', 'materials', 'employees', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        if (!$companyId) return back()->with('error', 'No active company.')->withInput();

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_material_id' => ['nullable', 'exists:supplier_materials,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', 'in:cash,transfer,cheque,card,other'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'paid_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('supplier-payments', 'public');
        }

        SupplierPayment::create([
            ...$data,
            'company_id' => $companyId,
            'paid_at' => $data['paid_at'] ?? now(),
            'attachment_path' => $path,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.supplier_payments.index')->with('success', 'Payment added.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        $payment = SupplierPayment::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with(['supplier', 'material', 'employee'])
            ->findOrFail($id);

        $suppliers = Supplier::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $materials = SupplierMaterial::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('title')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        return view('admin.supplier_payments.edit', compact('payment', 'suppliers', 'materials', 'employees', 'types'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        $payment = SupplierPayment::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_material_id' => ['nullable', 'exists:supplier_materials,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', 'in:cash,transfer,cheque,card,other'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'paid_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($payment->attachment_path)
                Storage::disk('public')->delete($payment->attachment_path);
            $payment->attachment_path = $request->file('attachment')->store('supplier-payments', 'public');
        }

        $payment->fill([...$data, 'updated_by' => auth()->id()])->save();

        return redirect()->route('voyager.supplier_payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        $payment = SupplierPayment::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if ($payment->attachment_path) Storage::disk('public')->delete($payment->attachment_path);

        $payment->delete();
        return back()->with('success', 'Payment deleted.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(SupplierPayment::class));

        $companyId = $this->activeCompanyId();
        $payment = SupplierPayment::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$payment->attachment_path || !Storage::disk('public')->exists($payment->attachment_path)) abort(404);

        return Storage::disk('public')->download($payment->attachment_path);
    }
}
