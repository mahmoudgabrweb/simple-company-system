<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectExpense;
use App\Models\Project;
use App\Models\Employee;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectExpenseController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();

        $q = ProjectExpense::with(['project', 'payer'])
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->orderByDesc('paid_at')->orderByDesc('id');

        // Filters
        if ($request->filled('project_id')) $q->where('project_id', $request->integer('project_id'));
        if ($request->filled('payment_type')) $q->where('payment_type', $request->string('payment_type'));
        if ($request->filled('date_from')) $q->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        if ($request->filled('date_to')) $q->where('paid_at', '<=', $request->date('date_to')->endOfDay());

        // ---- Summary totals (based on the same filtered query)
        $sumAll = (clone $q)->reorder()->sum('amount');

        $sumTypes = (clone $q)->reorder()
            ->select('payment_type')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('payment_type')
            ->pluck('total', 'payment_type');

        $paymentsMap = ['cash' => 'نقدًا', 'transfer' => 'تحويل', 'cheque' => 'شيك', 'card' => 'بطاقة', 'other' => 'أخرى'];

        $expenses = $q->paginate(20)->appends($request->query());
        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = $paymentsMap;

        return view('admin.project_expenses.index', compact('expenses', 'projects', 'types', 'sumAll', 'sumTypes', 'paymentsMap'));
    }

    public function create()
    {
        $this->authorize('add', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = ['cash' => 'نقدًا', 'transfer' => 'تحويل', 'cheque' => 'شيك', 'card' => 'بطاقة', 'other' => 'أخرى'];

        if (!$companyId) {
            session()->flash('error', 'لم يتم تحديد شركة نشطة — تم عرض كل المشاريع والموظفين مؤقتًا.');
        }

        return view('admin.project_expenses.create', compact('projects', 'employees', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        if (!$companyId) return back()->with('error', 'لا توجد شركة نشطة.')->withInput();

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'payment_type' => ['required', 'in:cash,transfer,cheque,card,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'], // <-- new
        ]);

        $projectCompanyId = DB::table('projects')->where('id', $data['project_id'])->value('company_id');
        if ((int)$projectCompanyId !== (int)$companyId) {
            return back()->with('error', 'هذا المشروع لا يتبع الشركة النشطة.')->withInput();
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('project-expenses', 'public');
        }

        ProjectExpense::create([
            'company_id' => $companyId,
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'paid_by_employee_id' => $data['paid_by_employee_id'] ?? null,
            'payment_type' => $data['payment_type'],
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? now(),
            'attachment_path' => $path,                         // <-- new
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('voyager.project_expenses.index')->with('success', 'تم إضافة المصروف.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        $expense = ProjectExpense::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with(['project', 'payer'])->findOrFail($id);

        $projects = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $employees = Employee::when($companyId, fn($x) => $x->where('company_id', $companyId))->orderBy('name')->get();
        $types = ['cash' => 'نقدًا', 'transfer' => 'تحويل', 'cheque' => 'شيك', 'card' => 'بطاقة', 'other' => 'أخرى'];

        return view('admin.project_expenses.edit', compact('expense', 'projects', 'employees', 'types'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        $expense = ProjectExpense::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'paid_by_employee_id' => ['nullable', 'exists:employees,id'],
            'payment_type' => ['required', 'in:cash,transfer,cheque,card,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'], // <-- new
        ]);

        $projectCompanyId = DB::table('projects')->where('id', $data['project_id'])->value('company_id');
        if ((int)$projectCompanyId !== (int)$companyId) {
            return back()->with('error', 'هذا المشروع لا يتبع الشركة النشطة.')->withInput();
        }

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path) Storage::disk('public')->delete($expense->attachment_path);
            $expense->attachment_path = $request->file('attachment')->store('project-expenses', 'public');
        }

        $expense->fill([
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'paid_by_employee_id' => $data['paid_by_employee_id'] ?? null,
            'payment_type' => $data['payment_type'],
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? $expense->paid_at,
            'updated_by' => auth()->id(),
        ])->save();

        return redirect()->route('voyager.project_expenses.index')->with('success', 'تم تحديث المصروف.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        $expense = ProjectExpense::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if ($expense->attachment_path) Storage::disk('public')->delete($expense->attachment_path);

        $expense->delete();
        return back()->with('success', 'تم حذف المصروف.');
    }

    public function download(int $id)
    {
        $this->authorize('read', app(ProjectExpense::class));

        $companyId = $this->activeCompanyId();
        $expense = ProjectExpense::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$expense->attachment_path || !Storage::disk('public')->exists($expense->attachment_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($expense->attachment_path);
    }
}
