<?php


namespace App\Http\Controllers\Admin;

use App\Models\SiteProject;
use App\Models\SiteProjectFeature;
use App\Models\SiteProjectImage;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SiteProjectController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'site_projects';
        $this->model = SiteProject::class;
    }

    // Voyager: GET voyager.projects.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $status = $request->get('status');
        $search = $request->get('search');

        $projects = SiteProject::query()
            ->with(['creator:id,name'])
            ->forCompany()
            ->when($status, fn($q) => $q->where('status', $status))
            ->search($search)
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        // Stats
        $statsQuery = SiteProject::query()->forCompany();
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'published' => (clone $statsQuery)->where('status', 'published')->count(),
            'drafts' => (clone $statsQuery)->where('status', 'draft')->count(),
            'featured' => (clone $statsQuery)->where('is_featured', true)->count(),
        ];

        return view('admin.site.projects.index', compact('projects', 'stats'));
    }

    // Custom: GET /admin/projects/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = SiteProject::query()
            ->with(['creator:id,name'])
            ->forCompany()
            ->select([
                'id', 'title', 'excerpt', 'category', 'client', 'status',
                'is_featured', 'display_order', 'created_by', 'created_at'
            ]);

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

    // Voyager: GET voyager.projects.create
    public function create()
    {
        $this->checkPermission('add');

        $project = new SiteProject();

        return view('admin.site.projects.create', compact('project'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.projects.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $request->validate([
            'title' => 'required|string|max:190',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'client' => 'nullable|string|max:190',
            'duration' => 'nullable|string|max:190',
            'category' => 'nullable|string|max:190',
            'cover_image' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',

            // Optional nested arrays (if you decide to post them)
            'images' => 'sometimes|array',
            'images.*.image_path' => 'required_with:images|string|max:255',
            'images.*.caption' => 'nullable|string|max:255',
            'images.*.display_order' => 'nullable|integer|min:0',
            'images.*.is_cover' => 'nullable|boolean',

            'features' => 'sometimes|array',
            'features.*.label' => 'required_with:features|string|max:255',
            'features.*.display_order' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $project = SiteProject::create($request->only([
                'title', 'excerpt', 'body', 'client', 'duration', 'category',
                'cover_image', 'status', 'display_order', 'is_featured'
            ]));

            // Images (optional)
            foreach ((array)$request->input('images', []) as $img) {
                if (!empty($img['image_path'])) {
                    $project->images()->create([
                        'image_path' => $img['image_path'],
                        'caption' => $img['caption'] ?? null,
                        'display_order' => (int)($img['display_order'] ?? 0),
                        'is_cover' => !empty($img['is_cover']),
                    ]);
                }
            }

            // Features (optional)
            foreach ((array)$request->input('features', []) as $feat) {
                if (!empty($feat['label'])) {
                    $project->features()->create([
                        'label' => $feat['label'],
                        'display_order' => (int)($feat['display_order'] ?? 0),
                    ]);
                }
            }
        });

        return redirect()->route('voyager.projects.index')
            ->with(['message' => 'تم إنشاء المشروع بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.projects.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $project = SiteProject::with(['images' => fn($q) => $q->ordered(), 'features' => fn($q) => $q->ordered()])
            ->forCompany()
            ->findOrFail($id);

        return view('admin.site.projects.edit', compact('project'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.projects.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $request->validate([
            'title' => 'required|string|max:190',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'client' => 'nullable|string|max:190',
            'duration' => 'nullable|string|max:190',
            'category' => 'nullable|string|max:190',
            'cover_image' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',

            'images' => 'sometimes|array',
            'images.*.id' => 'nullable|integer|exists:project_images,id',
            'images.*.image_path' => 'required_with:images|string|max:255',
            'images.*.caption' => 'nullable|string|max:255',
            'images.*.display_order' => 'nullable|integer|min:0',
            'images.*.is_cover' => 'nullable|boolean',

            'features' => 'sometimes|array',
            'features.*.id' => 'nullable|integer|exists:project_features,id',
            'features.*.label' => 'required_with:features|string|max:255',
            'features.*.display_order' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $project = SiteProject::forCompany()->findOrFail($id);
            $project->update($request->only([
                'title', 'excerpt', 'body', 'client', 'duration', 'category',
                'cover_image', 'status', 'display_order', 'is_featured'
            ]));

            // Sync images if provided (upsert-like)
            if ($request->has('images')) {
                $keepIds = [];
                foreach ((array)$request->input('images', []) as $img) {
                    if (!empty($img['id'])) {
                        // Update existing
                        $rec = SiteProjectImage::where('project_id', $project->id)->find($img['id']);
                        if ($rec) {
                            $rec->update([
                                'image_path' => $img['image_path'],
                                'caption' => $img['caption'] ?? null,
                                'display_order' => (int)($img['display_order'] ?? 0),
                                'is_cover' => !empty($img['is_cover']),
                            ]);
                            $keepIds[] = $rec->id;
                        }
                    } else {
                        // Create new
                        $rec = $project->images()->create([
                            'image_path' => $img['image_path'],
                            'caption' => $img['caption'] ?? null,
                            'display_order' => (int)($img['display_order'] ?? 0),
                            'is_cover' => !empty($img['is_cover']),
                        ]);
                        $keepIds[] = $rec->id;
                    }
                }
                // Remove images not present anymore
                SiteProjectImage::where('project_id', $project->id)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }

            // Sync features if provided
            if ($request->has('features')) {
                $keepIds = [];
                foreach ((array)$request->input('features', []) as $feat) {
                    if (!empty($feat['id'])) {
                        $rec = SiteProjectFeature::where('project_id', $project->id)->find($feat['id']);
                        if ($rec) {
                            $rec->update([
                                'label' => $feat['label'],
                                'display_order' => (int)($feat['display_order'] ?? 0),
                            ]);
                            $keepIds[] = $rec->id;
                        }
                    } else {
                        $rec = $project->features()->create([
                            'label' => $feat['label'],
                            'display_order' => (int)($feat['display_order'] ?? 0),
                        ]);
                        $keepIds[] = $rec->id;
                    }
                }
                SiteProjectFeature::where('project_id', $project->id)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }
        });

        return redirect()->route('voyager.projects.index')
            ->with(['message' => 'تم تحديث المشروع بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.projects.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $project = SiteProject::forCompany()->findOrFail($id);
        $project->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
