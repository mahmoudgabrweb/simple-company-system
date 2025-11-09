<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'services';
        $this->model = Service::class;
    }

    // Voyager: GET voyager.services.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $status = $request->get('status');
        $search = $request->get('search');

        $services = Service::query()
            ->with(['creator:id,name'])
            ->forCompany()
            ->when($status, fn($q) => $q->where('status', $status))
            ->search($search)
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        // Stats
        $statsQuery = Service::query()->forCompany();
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'published' => (clone $statsQuery)->where('status', 'published')->count(),
            'drafts' => (clone $statsQuery)->where('status', 'draft')->count(),
            'featured' => (clone $statsQuery)->where('is_featured', true)->count(),
        ];

        return view('admin.site.services.index', compact('services', 'stats'));
    }

    // Custom: GET /admin/services/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Service::query()
            ->with(['creator:id,name'])
            ->forCompany()
            ->select(['id', 'title', 'excerpt', 'status', 'is_featured', 'display_order', 'created_by', 'created_at']);

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $module = $this->moduleName;

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('creator', fn($r) => $r->creator?->name ?? '—')
            ->addColumn('featured', fn($r) => $r->is_featured ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-light text-dark">No</span>')
            ->editColumn('status', fn($r) => match ($r->status) {
                'published' => '<span class="badge bg-success">Published</span>',
                'draft' => '<span class="badge bg-secondary">Draft</span>',
                'archived' => '<span class="badge bg-dark">Archived</span>',
                default => $r->status,
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
            ->rawColumns(['actions', 'featured', 'status'])
            ->make();
    }

    // Voyager: GET voyager.services.create
    public function create()
    {
        $this->checkPermission('add');
        $service = new Service();

        return view('admin.site.services.create', compact('service'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.services.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $request->validate([
            'title' => 'required|string|max:190',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'icon' => 'nullable|string|max:190',
            'status' => 'required|in:draft,published,archived',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
        ]);

        Service::create($request->only(['title', 'excerpt', 'body', 'icon', 'status', 'display_order', 'is_featured']));

        return redirect()->route('voyager.services.index')
            ->with(['message' => 'تم إنشاء الخدمة بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.services.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');
        $service = Service::findOrFail($id);

        return view('admin.site.services.edit', compact('service'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.services.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            'title' => 'required|string|max:190',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'icon' => 'nullable|string|max:190',
            'status' => 'required|in:draft,published,archived',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->only(['title', 'excerpt', 'body', 'icon', 'status', 'display_order', 'is_featured']));

        return redirect()->route('voyager.services.index')
            ->with(['message' => 'تم تحديث الخدمة بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.services.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
