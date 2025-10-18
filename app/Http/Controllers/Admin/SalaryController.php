<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
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

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $month = trim((string)$request->get('month'));  // YYYY-MM
        $employee_id = $request->integer('employee_id');
        $project_id = $request->integer('project_id');

        $companyId = CompanyContext::id();

        $query = Salary::with(['project', 'employee'])
            // scope by active company via the employee relation
            ->when($companyId, fn($q) => $q->whereHas('employee', fn($qq) => $qq->where('company_id', $companyId))
            );

        // Filters
        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }
        if ($month !== '') {
            // your column is `month` (date), not salary_date
            $query->whereRaw("DATE_FORMAT(`month`, '%Y-%m') = ?", [$month]);
        }
        if ($project_id) {
            $query->where('project_id', $project_id);
        }

        // Totals from the SAME filtered query
        $salaries = $query->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        // Projects dropdown (use the correct name column for your schema)
        $projects = Project::orderBy('id', 'desc')->select('id', 'name')->get();
        $employees = Employee::orderBy('id', 'desc')->select('id', 'name')->get();

        $sumAll = (clone $query)->reorder()->sum('amount');

        $sumTypes = (clone $query)->reorder()
            ->select('type')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $paymentsMap = [
            'salary' => 'salary',
            'overtime' => 'overtime',
            'bonus' => 'bonus',
        ];

        $totalDays = (int)(clone $query)->reorder()->sum('days_count');

        return view('admin.salaries.index', [
            'salaries' => $salaries,
            'filters' => compact('month', 'project_id', 'employee_id'),
            'employees' => $employees,
            'projects' => $projects,
            'sumAll' => $sumAll,
            'sumTypes' => $sumTypes,
            'paymentsMap' => $paymentsMap,
            'totalDays' => $totalDays, // optional
        ]);
    }
//    public function index(Request $request)
//    {
//        $this->checkPermission('browse');
//
//        $salaries = Salary::query()
//            ->with([
//                'employee:id,name',             // if relation exists
//                'expenseType:id,name',          // if relation exists
//            ])
//            ->orderByDesc('id')
//            ->paginate(15)
//            ->withQueryString();
//
//        // Stats
//        $stats = [
//            'total'  => Salary::count(),
//            'amount' => Salary::sum('amount'),
//        ];
//
//        return view('admin.salaries.index', compact('salaries', 'stats'));
//    }

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
        $projects = Project::orderBy('id', 'desc')->select('id', 'name')->get();

        return view('admin.salaries.create', compact('salary', 'employees', 'types', 'projects'))
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
            'project_id' => ['nullable', 'exists:projects,id'],
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

        $data = $request->only(['title', 'project_id', 'employee_id', 'expense_type_id', 'type', 'amount', 'days_count']);
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
        $projects = Project::orderBy('id', 'desc')->select('id', 'name')->get();

        return view('admin.salaries.edit', compact('salary', 'employees', 'types', 'projects'))
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
            'project_id' => ['nullable', 'exists:projects,id'],
            'month' => ['required', 'date_format:Y-m'],
            'expense_type_id' => ['required', 'integer', 'exists:expense_types,id'],
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

        $data = $request->only(['title', 'project_id', 'employee_id', 'expense_type_id', 'type', 'amount', 'days_count']);
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
