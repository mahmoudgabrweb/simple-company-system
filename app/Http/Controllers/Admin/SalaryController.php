<?php

namespace App\Http\Controllers\Admin;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'salaries';
        $this->model = Salary::class;
    }

    // Voyager: GET voyager.salaries.index
    public function index()
    {
        $this->checkPermission('browse');

        $cid = CompanyContext::id();
        $stats = [
            'total' => Salary::query()
                ->join('employees', 'employees.id', '=', 'salaries.employee_id')
                ->where('employees.company_id', $cid)->count(),
            'amount' => (float)Salary::query()
                ->join('employees', 'employees.id', '=', 'salaries.employee_id')
                ->where('employees.company_id', $cid)->sum('amount'),
        ];

        return view('admin.salaries.index', compact('stats'));
    }

    // Custom: GET /admin/salaries/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $cid = CompanyContext::id();

        $q = Salary::query()
            ->with(['employee:id,name', 'expenseType:id,name'])
            ->select(['salaries.id', 'salaries.title', 'salaries.employee_id', 'salaries.month', 'salaries.type', 'salaries.amount', 'salaries.days_count', 'salaries.created_at'])
            ->join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->where('employees.company_id', $cid);

        $module = $this->moduleName;

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('employee', fn($r) => $r->employee?->name ?? '—')
            ->addColumn('expense_type', fn($r) => $r->expenseType?->name ?? '—')
            ->editColumn('month', fn($r) => $r->month?->format('Y-m') ?? '—')
            ->editColumn('type', function ($r) {
                return match ($r->type) {
                    'salary' => 'راتب',
                    'overtime' => 'وقت إضافي',
                    'bonus' => 'مكافأة',
                    default => $r->type,
                };
            })
            ->editColumn('amount', fn($r) => number_format((float)$r->amount, 2))
            ->editColumn('created_at', fn($r) => $r->created_at?->format('Y-m-d H:i'))
            ->addColumn('actions', function ($r) use ($module) {
                $u = auth()->user();
                $btns = '';
                if ($u->hasPermission("edit_{$module}")) {
                    $btns .= "<a href='" . route("voyager.$module.edit", $r->id) . "' class='btn btn-sm btn-warning'>تعديل</a> ";
                }
                if ($u->hasPermission("delete_{$module}")) {
                    $btns .= "<a href='javascript:void(0);' data-url='" . route("voyager.$module.destroy", $r->id) . "' data-id='{$r->id}' class='delete-record btn btn-sm btn-danger'>حذف</a>";
                }
                return $btns ?: '—';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // Voyager: GET voyager.salaries.create
    public function create()
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();

        $salary = new Salary();
        $employees = Employee::where('company_id', $cid)->orderBy('name')->get(['id', 'name']);
        $types = ExpenseType::where('company_id', $cid)->orderBy('name')->get(['id', 'name']);

        return view('admin.salaries.create', compact('salary', 'employees', 'types'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.salaries.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();

        $request->validate([
            'title' => 'required|string|max:190',
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'month' => ['required', 'date_format:Y-m'], // HTML <input type="month">
            'expense_type_id' => ['nullable', 'integer', 'exists:expense_types,id'],
            'type' => ['required', Rule::in(['salary', 'overtime', 'bonus'])],
            'amount' => 'required|numeric|min:0|max:9999999999.99',
            'days_count' => 'nullable|integer|min:0|max:365',
        ]);

        // Enforce employee belongs to current company
        if (!Employee::where('id', $request->employee_id)->where('company_id', $cid)->exists()) {
            return back()->withErrors(['employee_id' => 'هذا الموظف لا ينتمي إلى الشركة الحالية'])->withInput();
        }

        // Enforce expense type belongs to current company (if provided)
        if ($request->filled('expense_type_id')
            && !ExpenseType::where('id', $request->expense_type_id)->where('company_id', $cid)->exists()) {
            return back()->withErrors(['expense_type_id' => 'نوع المصروف لا ينتمي إلى الشركة الحالية'])->withInput();
        }

        $data = $request->only(['title', 'employee_id', 'expense_type_id', 'type', 'amount', 'days_count']);
        // Convert YYYY-MM -> YYYY-MM-01
        $data['month'] = $request->input('month') . '-01';

        Salary::create($data);

        return redirect()->route('voyager.salaries.index')
            ->with(['message' => 'تم إنشاء السجل بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.salaries.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();

        $salary = Salary::query()
            ->join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->where('employees.company_id', $cid)
            ->where('salaries.id', $id)
            ->select('salaries.*')
            ->firstOrFail();

        $employees = Employee::where('company_id', $cid)->orderBy('name')->get(['id', 'name']);
        $types = ExpenseType::where('company_id', $cid)->orderBy('name')->get(['id', 'name']);

        return view('admin.salaries.edit', compact('salary', 'employees', 'types'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.salaries.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();

        $request->validate([
            'title' => 'required|string|max:190',
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'month' => ['required', 'date_format:Y-m'],
            'expense_type_id' => ['nullable', 'integer', 'exists:expense_types,id'],
            'type' => ['required', Rule::in(['salary', 'overtime', 'bonus'])],
            'amount' => 'required|numeric|min:0|max:9999999999.99',
            'days_count' => 'nullable|integer|min:0|max:365',
        ]);

        if (!Employee::where('id', $request->employee_id)->where('company_id', $cid)->exists()) {
            return back()->withErrors(['employee_id' => 'هذا الموظف لا ينتمي إلى الشركة الحالية'])->withInput();
        }
        if ($request->filled('expense_type_id')
            && !ExpenseType::where('id', $request->expense_type_id)->where('company_id', $cid)->exists()) {
            return back()->withErrors(['expense_type_id' => 'نوع المصروف لا ينتمي إلى الشركة الحالية'])->withInput();
        }

        $salary = Salary::query()
            ->join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->where('employees.company_id', $cid)
            ->where('salaries.id', $id)
            ->select('salaries.*')
            ->firstOrFail();

        $data = $request->only(['title', 'employee_id', 'expense_type_id', 'type', 'amount', 'days_count']);
        $data['month'] = $request->input('month') . '-01';

        $salary->update($data);

        return redirect()->route('voyager.salaries.index')
            ->with(['message' => 'تم تحديث السجل بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.salaries.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $cid = CompanyContext::id();

        $salary = Salary::query()
            ->join('employees', 'employees.id', '=', 'salaries.employee_id')
            ->where('employees.company_id', $cid)
            ->where('salaries.id', $id)
            ->select('salaries.*')
            ->firstOrFail();

        $salary->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
