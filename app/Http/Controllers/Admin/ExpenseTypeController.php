<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExpenseType;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'expense_types';
        $this->model = ExpenseType::class;
    }

    // Voyager: GET voyager.expense_types.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $expenseTypes = ExpenseType::query()
            ->where("company_id", CompanyContext::id())
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = ['total' => ExpenseType::count()];

        return view('admin.expense_types.index', compact('expenseTypes', 'stats', 'q'));
    }

    // Custom: GET /admin/expense_types/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = ExpenseType::query()->where("company_id", CompanyContext::id())->select(['id', 'name', 'created_at']);
        $module = $this->moduleName;

        return DataTables::of($q)
            ->addIndexColumn()
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

    // Voyager: GET voyager.expense_types.create
    public function create()
    {
        $this->checkPermission('add');
        $expenseType = new ExpenseType();
        return view('admin.expense_types.create', compact('expenseType'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.expense_types.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();
        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                Rule::unique('expense_types', 'name')->where(fn($q) => $q->where('company_id', $cid)),
            ],
        ]);

        ExpenseType::create([
            'name' => (string)$request->input('name'),
        ]);

        return redirect()->route('voyager.expense_types.index')
            ->with(['message' => 'تم إنشاء نوع المصروف بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.expense_types.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');
        $expenseType = ExpenseType::findOrFail($id);

        return view('admin.expense_types.edit', compact('expenseType'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.expense_types.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();
        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                Rule::unique('expense_types', 'name')
                    ->where(fn($q) => $q->where('company_id', $cid))
                    ->ignore($id),
            ],
        ]);

        $expenseType = ExpenseType::findOrFail($id);
        $expenseType->update(['name' => (string)$request->input('name')]);

        return redirect()->route('voyager.expense_types.index')
            ->with(['message' => 'تم تحديث نوع المصروف بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.expense_types.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');
        $expenseType = ExpenseType::findOrFail($id);
        $expenseType->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
