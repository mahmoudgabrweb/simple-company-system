<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Models\Client;
use App\Models\City;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'projects';
        $this->model = Project::class;
    }

    public function index(Request $request)
    {
        $this->authorize('browse', app(\App\Models\Project::class));

        $companyId = session('active_company_id')
            ?? session('current_company_id')
            ?? (auth()->user()->company_id ?? null);

        // Base query + simple search (name/address)
        $q = \App\Models\Project::query()
            ->with(['client', 'city'])
            ->when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->when($request->filled('q'), function ($x) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $x->where(function ($w) use ($term) {
                    $w->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->orderByDesc('id');

        // Paginate the visible projects
        $projects = $q->paginate(15)->appends($request->query());

        // Pre-compute financials for the listed projects only
        $projectIds = $projects->pluck('id');

        // Total (from active quotation)
        $activeTotals = \App\Models\Quotation::select('project_id', \DB::raw('MAX(total_amount) as total_amount'))
            ->whereIn('project_id', $projectIds)
            ->where('is_active', true)
            ->groupBy('project_id')
            ->pluck('total_amount', 'project_id');

        // Paid (payments sum)
        $paidTotals = \App\Models\Payment::select('project_id', \DB::raw('SUM(amount) as total'))
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        // Expenses (project expenses sum)
        $expenseTotals = \App\Models\ProjectExpense::select('project_id', \DB::raw('SUM(amount) as total'))
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        // Map into finance array keyed by project_id
        $finance = [];
        foreach ($projectIds as $pid) {
            $total = (float)($activeTotals[$pid] ?? 0);
            $paid = (float)($paidTotals[$pid] ?? 0);
            $exp = (float)($expenseTotals[$pid] ?? 0);
            $remaining = $total - ($paid + $exp);
            $finance[$pid] = compact('total', 'paid', 'exp', 'remaining');
        }

        return view('admin.projects.index', compact('projects', 'finance'));
    }


    // Voyager: GET voyager.projects.index
//    public function index()
//    {
//        $this->checkPermission('browse');
//
//        $stats = ['total' => Project::count()];
//        return view('admin.projects.index', compact('stats'));
//    }

    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Project::query()
            ->with(['client:id,name', 'city:id,name'])
            ->select(['id', 'name', 'client_id', 'city_id', 'address', 'created_at']);

        $module = $this->moduleName;

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('client', fn($r) => $r->client?->name ?? '—')
            ->addColumn('city', fn($r) => $r->city?->name ?? '—')
            ->editColumn('address', function ($r) {
                $txt = trim(strip_tags((string)$r->address));
                return mb_strlen($txt) > 60 ? mb_substr($txt, 0, 60) . '…' : $txt;
            })
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
            ->make();
    }

    // Voyager: GET voyager.projects.create
    public function create()
    {
        $this->checkPermission('add');

        $project = new Project();
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('admin.projects.create', compact('project', 'clients', 'cities'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.projects.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                // Rule::unique('projects','name')->where(fn($q)=>$q->where('company_id',$cid)),
            ],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:190',
            'map' => 'nullable|string',
        ]);

        Project::create($request->only(['name', 'client_id', 'city_id', 'address', 'location', 'map']));

        return redirect()->route('voyager.projects.index')
            ->with(['message' => 'تم إنشاء المشروع بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.projects.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $project = Project::findOrFail($id);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('admin.projects.edit', compact('project', 'clients', 'cities'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.projects.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                // Rule::unique('projects','name')->where(fn($q)=>$q->where('company_id',$cid))->ignore($id),
            ],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:190',
            'map' => 'nullable|string',
        ]);

        $project = Project::findOrFail($id);
        $project->update($request->only(['name', 'client_id', 'city_id', 'address', 'location', 'map']));

        return redirect()->route('voyager.projects.index')
            ->with(['message' => 'تم تحديث المشروع بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.projects.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
