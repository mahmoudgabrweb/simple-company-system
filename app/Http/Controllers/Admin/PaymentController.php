<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Employee;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('browse', app(Payment::class));

        $companyId = CompanyContext::id();

        $q = Payment::with(['project', 'receiver'])
            ->where('company_id', $companyId)
            ->orderByDesc('paid_at')
            ->orderByDesc('id');

        // Filters: project, method, date range
        if ($request->filled('project_id')) {
            $q->where('project_id', $request->integer('project_id'));
        }
        if ($request->filled('payment_method')) {
            $q->where('payment_method', $request->string('payment_method'));
        }
        if ($request->filled('date_from')) {
            $q->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        }
        if ($request->filled('date_to')) {
            $q->where('paid_at', '<=', $request->date('date_to')->endOfDay());
        }

        $payments = $q->paginate(20)->appends($request->query());

        $projects = Project::where('company_id', $companyId)->orderBy('name')->get();
        $methods = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        return view('admin.payments.index', compact('payments', 'projects', 'methods'));
    }

    public function create()
    {
        $this->authorize('add', app(Payment::class));

        $companyId = CompanyContext::id();
        $projects = Project::where('company_id', $companyId)->orderBy('name')->get();
        $employees = Employee::where('company_id', $companyId)->orderBy('name')->get();
        $methods = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        return view('admin.payments.create', compact('projects', 'employees', 'methods'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(Payment::class));

        $companyId = CompanyContext::id();
        if (!$companyId) return back()->with('error', 'لا توجد شركة نشطة.')->withInput();

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,transfer,cheque,card'],
            'paid_at' => ['nullable', 'date'],
            'paid_by' => ['nullable', 'string', 'max:190'],
            'received_by_employee_id' => ['nullable', 'exists:employees,id'],
            'reference' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        // ensure project belongs to active company
        $projectCompanyId = \DB::table('projects')->where('id', $data['project_id'])->value('company_id');
        if ((int)$projectCompanyId !== (int)$companyId) {
            return back()->with('error', 'هذا المشروع لا يتبع الشركة النشطة.')->withInput();
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('payments', 'public');
        }

        Payment::create([
            'company_id' => $companyId,
            'project_id' => $data['project_id'],
            'paid_by' => $data['paid_by'] ?? null,
            'received_by_employee_id' => $data['received_by_employee_id'] ?? null,
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? now(),
            'attachment_path' => $path,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.payments.index')->with('success', 'تم إضافة الدفعة.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(Payment::class));

        $companyId = CompanyContext::id();
        $payment = Payment::where('company_id', $companyId)->with(['project', 'receiver'])->findOrFail($id);
        $projects = Project::where('company_id', $companyId)->orderBy('name')->get();
        $employees = Employee::where('company_id', $companyId)->orderBy('name')->get();
        $methods = ['cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'];

        return view('admin.payments.edit', compact('payment', 'projects', 'employees', 'methods'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(Payment::class));

        $companyId = CompanyContext::id();
        $payment = Payment::where('company_id', $companyId)->findOrFail($id);

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,transfer,cheque,card'],
            'paid_at' => ['nullable', 'date'],
            'paid_by' => ['nullable', 'string', 'max:190'],
            'received_by_employee_id' => ['nullable', 'exists:employees,id'],
            'reference' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        // optional consistency check again
        $projectCompanyId = \DB::table('projects')->where('id', $data['project_id'])->value('company_id');
        if ((int)$projectCompanyId !== (int)$companyId) {
            return back()->with('error', 'هذا المشروع لا يتبع الشركة النشطة.')->withInput();
        }

        if ($request->hasFile('attachment')) {
            if ($payment->attachment_path) Storage::disk('public')->delete($payment->attachment_path);
            $payment->attachment_path = $request->file('attachment')->store('payments', 'public');
        }

        $payment->fill([
            'project_id' => $data['project_id'],
            'paid_by' => $data['paid_by'] ?? null,
            'received_by_employee_id' => $data['received_by_employee_id'] ?? null,
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? $payment->paid_at,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_by' => auth()->id(),
        ])->save();

        return redirect()->route('voyager.payments.index')->with('success', 'تم تحديث الدفعة.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(Payment::class));
        $companyId = CompanyContext::id();
        $payment = Payment::where('company_id', $companyId)->findOrFail($id);
        if ($payment->attachment_path) Storage::disk('public')->delete($payment->attachment_path);
        $payment->delete();
        return back()->with('success', 'تم حذف الدفعة.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(Payment::class));
        $companyId = CompanyContext::id();
        $payment = Payment::where('company_id', $companyId)->findOrFail($id);
        if (!$payment->attachment_path || !Storage::disk('public')->exists($payment->attachment_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($payment->attachment_path);
    }
}
