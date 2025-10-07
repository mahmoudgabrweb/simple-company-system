<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\Job;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JobController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'jobs';
        $this->model = Job::class;
    }

    // ───────────────── Voyager: GET voyager.jobs.index
    public function index()
    {
        $this->checkPermission('browse');

        $stats = [
            'total' => Job::count(),
            'active' => Job::where('is_active', 1)->count(),
            'inactive' => Job::where('is_active', 0)->count(),
        ];

        return view('admin.jobs.index', compact('stats'));
    }

    // ───────────────── Custom: GET /admin/jobs/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Job::query()->select(['id', 'title', 'description', 'is_active', 'created_at']);

        return DataTables::of($q)
            ->addIndexColumn()
            ->editColumn('description', function ($row) {
                $txt = strip_tags($row->description ?? '');
                return mb_strlen($txt) > 80 ? mb_substr($txt, 0, 80) . '…' : $txt;
            })
            ->editColumn('is_active', function ($row) {
                $cls = $row->is_active ? 'success' : 'danger';
                $txt = $row->is_active ? 'Active' : 'Inactive';
                return '<span class="label label-' . $cls . '">' . $txt . '</span>';
            })
            ->editColumn('created_at', fn($r) => $r->created_at?->format('Y-m-d H:i'))
            ->addColumn('actions', function ($r) {
                $module = $this->moduleName; // e.g. 'jobs'
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
//            ->addColumn('actions', function ($row) {
//                $editUrl   = route('voyager.jobs.edit', $row->id);
//                $toggleUrl = route('voyager.jobs.toggle', $row->id);
//                $deleteUrl = route('voyager.jobs.destroy', $row->id);
//
//                if (view()->exists('admin.jobs.partials.actions')) {
//                    return view('admin.jobs.partials.actions', compact('editUrl','toggleUrl','deleteUrl','row'))->render();
//                }
//                // Fallback inline HTML
//                return '<div class="btn-group" role="group">
//                    <a href="'.$editUrl.'" class="btn btn-sm btn-primary">Edit <i class="voyager-edit"></i></a>
//                    <button type="button" class="btn btn-sm btn-warning js-toggle" data-url="'.$toggleUrl.'">Action <i class="voyager-power"></i></button>
//                    <button type="button" class="btn btn-sm btn-danger js-delete" data-url="'.$deleteUrl.'">Delete <i class="voyager-trash"></i></button>
//                </div>';
//            })
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    // ───────────────── Voyager: GET voyager.jobs.create
    public function create()
    {
        $this->checkPermission('add');
        $job = new Job();
        return view('admin.jobs.create', compact('job'))
            ->with('currentCompany', CompanyContext::company());
    }

    // ───────────────── Voyager: POST voyager.jobs.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:190',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            Job::create([
                // company_id is auto-filled by BelongsToCompany trait from session context
                'title' => (string)$request->input('title'),
                'description' => $request->input('description'),
                'is_active' => (bool)$request->input('is_active'),
            ]);
        });

        return redirect()->route('voyager.jobs.index')
            ->with(['message' => 'تم إنشاء الوظيفة بنجاح', 'alert-type' => 'success']);
    }

    // ───────────────── Voyager: GET voyager.jobs.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $job = Job::findOrFail($id);

        return view('admin.jobs.edit', compact('job'))
            ->with('currentCompany', CompanyContext::company());
    }

    // ───────────────── Voyager: PUT/PATCH voyager.jobs.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:190',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $id) {
            $job = Job::findOrFail($id);
            $job->update([
                'title' => (string)$request->input('title'),
                'description' => $request->input('description'),
                'is_active' => (bool)$request->input('is_active'),
            ]);
        });

        return redirect()->route('voyager.jobs.index')
            ->with(['message' => 'تم تحديث الوظيفة بنجاح', 'alert-type' => 'success']);
    }

    // ───────────────── Custom: POST /admin/jobs/{id}/toggle
    public function toggle(int $id): JsonResponse
    {
        $this->checkPermission('edit');

        $job = Job::findOrFail($id);
        $job->is_active = !$job->is_active;
        $job->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الحالة',
            'is_active' => $job->is_active,
        ]);
    }

    // ───────────────── Voyager: DELETE voyager.jobs.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
