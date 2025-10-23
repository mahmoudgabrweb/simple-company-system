<?php


namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Job;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'employees';
        $this->model = Employee::class;
    }

    // Voyager: GET voyager.employees.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $employees = Employee::query()
            ->with(['job' => fn($q) => $q->select('id', 'title')]) // if relation exists
            ->where("company_id", CompanyContext::id())
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::when(function ($q) {
                // only count if column exists; fall back to 0 if not
                try {
                    $q->where('is_active', 1);
                } catch (\Throwable $e) {
                }
            })->count(),
            'inactive' => Employee::when(function ($q) {
                try {
                    $q->where('is_active', 0);
                } catch (\Throwable $e) {
                }
            })->count(),
        ];

        return view('admin.employees.index', compact('employees', 'stats', 'q'));
    }

    // Custom: GET /admin/employees/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Employee::query()
            ->with('job:id,title')
            ->where("company_id", CompanyContext::id())
            ->select(['id', 'name', 'phone', 'email', 'job_id', 'start_at', 'end_at', 'salary', 'created_at']);

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('job', fn($r) => $r->job?->title ?? '—')
            ->addColumn('status', function ($r) {
                $active = is_null($r->end_at);
                $cls = $active ? 'success' : 'danger';
                $txt = $active ? 'Active' : 'Inactive';
                return '<span class="label label-' . $cls . '">' . $txt . '</span>';
            })
            ->editColumn('start_at', fn($r) => $r->start_at?->format('Y-m-d') ?? '—')
            ->editColumn('end_at', fn($r) => $r->end_at?->format('Y-m-d') ?? '—')
            ->editColumn('salary', fn($r) => number_format((float)$r->salary, 2))
            ->editColumn('created_at', fn($r) => $r->created_at?->format('Y-m-d H:i'))
            ->addColumn('actions', function ($r) {
                $module = $this->moduleName; // 'employees'
                $u = auth()->user();
                $btns = '';

                if ($u->hasPermission("edit_{$module}")) {
                    $btns .= "<a href='" . route("voyager.$module.edit", $r->id) . "' class='btn btn-sm btn-warning'>تعديل</a> ";
                }

                if ($u->hasPermission("edit_{$module}")) {
                    $btns .= "<a href='javascript:void(0);' data-url='" . route("voyager.$module.toggle", $r->id) . "' data-id='{$r->id}' class='js-toggle btn btn-sm btn-secondary'>تبديل</a> ";
                }

                if ($u->hasPermission("delete_{$module}")) {
                    $btns .= "<a href='javascript:void(0);' data-url='" . route("voyager.$module.destroy", $r->id) . "' data-id='{$r->id}' class='delete-record btn btn-sm btn-danger'>حذف</a>";
                }

                return $btns ?: '—';
            })
            ->rawColumns(['status', 'actions'])
            ->make();
    }

    // Voyager: GET voyager.employees.create
    public function create()
    {
        $this->checkPermission('add');

        $employee = new Employee();
        $jobs = Job::where("company_id", CompanyContext::id())->orderBy('title')->get(['id', 'title']);

        return view('admin.employees.create', compact('employee', 'jobs'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.employees.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => 'required|string|max:190',
            'phone' => 'required|string|max:50',
            'email' => [
                'nullable', 'email', 'max:190',
                Rule::unique('employees', 'email')->where(fn($q) => $q->where('company_id', $cid)),
            ],
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'salary' => 'required|numeric|min:0|max:9999999999.99',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:10240',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['name', 'phone', 'email', 'job_id', 'start_at', 'end_at', 'salary']);

            if ($request->hasFile('cv')) {
                $data['cv_path'] = $request->file('cv')->store('employees/cv', 'public');
            }
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('employees/images', 'public');
            }

            Employee::create($data);
        });

        return redirect()->route('voyager.employees.index')
            ->with(['message' => 'تم إنشاء الموظف بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.employees.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $employee = Employee::findOrFail($id);
        $jobs = Job::where("company_id", CompanyContext::id())->orderBy('title')->get(['id', 'title']);

        return view('admin.employees.edit', compact('employee', 'jobs'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.employees.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => 'required|string|max:190',
            'phone' => 'required|string|max:50',
            'email' => [
                'nullable', 'email', 'max:190',
                Rule::unique('employees', 'email')
                    ->where(fn($q) => $q->where('company_id', $cid))
                    ->ignore($id),
            ],
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'salary' => 'required|numeric|min:0|max:9999999999.99',
            'cv' => 'nullable|mimes:pdf,doc,docx|max:10240',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $id) {
            $emp = Employee::findOrFail($id);

            $data = $request->only(['name', 'phone', 'email', 'job_id', 'start_at', 'end_at', 'salary']);

            if ($request->hasFile('cv')) {
                if ($emp->cv_path) Storage::disk('public')->delete($emp->cv_path);
                $data['cv_path'] = $request->file('cv')->store('employees/cv', 'public');
            }
            if ($request->hasFile('image')) {
                if ($emp->image_path) Storage::disk('public')->delete($emp->image_path);
                $data['image_path'] = $request->file('image')->store('employees/images', 'public');
            }

            $emp->update($data);
        });

        return redirect()->route('voyager.employees.index')
            ->with(['message' => 'تم تحديث الموظف بنجاح', 'alert-type' => 'success']);
    }

    // Custom: POST /admin/employees/{id}/toggle
    public function toggle(int $id): JsonResponse
    {
        $this->checkPermission('edit');

        $emp = Employee::findOrFail($id);

        if (is_null($emp->end_at)) {
            $emp->end_at = Carbon::today();
        } else {
            $emp->end_at = null;
        }
        $emp->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الحالة',
            'active' => is_null($emp->end_at),
        ]);
    }

    // Voyager: DELETE voyager.employees.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $emp = Employee::findOrFail($id);

        if ($emp->cv_path) Storage::disk('public')->delete($emp->cv_path);
        if ($emp->image_path) Storage::disk('public')->delete($emp->image_path);

        $emp->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
