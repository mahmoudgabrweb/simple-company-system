<?php

namespace App\Http\Controllers\Admin;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'expenses';
        $this->model = Expense::class;
    }

    // Voyager: GET voyager.expenses.index
    public function index()
    {
        $this->checkPermission('browse');

        $stats = [
            'total' => Expense::count(),
            'amount' => (float)Expense::sum('amount'),
        ];

        return view('admin.expenses.index', compact('stats'));
    }

    // Custom: GET /admin/expenses/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Expense::query()
            ->with(['type:id,name','creator:id,name'])
            ->select(['id','title','amount','expense_type_id','created_by','spent_at','created_at']);

        // Optional filter by a specific day
        if ($request->filled('spent_at')) {
            $q->whereDate('spent_at', $request->input('spent_at'));
        }

        $module = $this->moduleName;

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('type', fn($r) => $r->type?->name ?? '—')
            ->addColumn('by',   fn($r) => $r->creator?->name ?? '—')
            ->editColumn('amount', fn($r) => number_format((float)$r->amount, 2))
            ->addColumn('spent_at', fn($r) => optional($r->spent_at)->format('Y-m-d') ?? '—')
            ->editColumn('created_at', fn($r) => $r->created_at?->format('Y-m-d H:i'))
            ->addColumn('actions', function ($r) use ($module) {
                $u = auth()->user();
                $btns = '';
                if ($u->hasPermission("edit_{$module}")) {
                    $btns .= "<a href='".route("voyager.$module.edit", $r->id)."' class='btn btn-sm btn-warning'>تعديل</a> ";
                }
                if ($u->hasPermission("delete_{$module}")) {
                    $btns .= "<a href='javascript:void(0);' data-url='".route("voyager.$module.destroy", $r->id)."' data-id='{$r->id}' class='delete-record btn btn-sm btn-danger'>حذف</a>";
                }
                return $btns ?: '—';
            })
            ->rawColumns(['actions'])
            ->make();
    }
    // Voyager: GET voyager.expenses.create
    public function create()
    {
        $this->checkPermission('add');

        $expense = new Expense();
        $types = ExpenseType::orderBy('name')->get(['id', 'name']);

        return view('admin.expenses.create', compact('expense', 'types'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.expenses.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $request->validate([
            'title'           => 'required|string|max:190',
            'amount'          => 'required|numeric|min:0|max:9999999999.99',
            'description'     => 'nullable|string',
            'spent_at'        => 'nullable|date', // NEW
            'expense_type_id' => ['required','integer','exists:expense_types,id'],
        ]);

        Expense::create($request->only(['title','amount','description','spent_at','expense_type_id']));

        return redirect()->route('voyager.expenses.index')
            ->with(['message' => 'تم إنشاء المصروف بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.expenses.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $expense = Expense::findOrFail($id);
        $types = ExpenseType::orderBy('name')->get(['id', 'name']);

        return view('admin.expenses.edit', compact('expense', 'types'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.expenses.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            'title'           => 'required|string|max:190',
            'amount'          => 'required|numeric|min:0|max:9999999999.99',
            'description'     => 'nullable|string',
            'spent_at'        => 'nullable|date', // NEW
            'expense_type_id' => ['required','integer','exists:expense_types,id'],
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($request->only(['title','amount','description','spent_at','expense_type_id']));

        return redirect()->route('voyager.expenses.index')
            ->with(['message' => 'تم تحديث المصروف بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.expenses.destroy (soft delete)
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
