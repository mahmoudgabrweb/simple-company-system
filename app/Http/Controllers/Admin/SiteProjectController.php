<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\SiteProject;
use App\Models\SiteProjectAchievement;
use App\Models\SiteProjectSlider;
use App\Services\UploaderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class SiteProjectController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'site_projects';
        $this->model = SiteProject::class;
    }

    // Voyager: GET voyager.site_projects.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $projects = SiteProject::query()
            ->withCount(['achievements', 'sliders'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('short_description', 'like', "%{$q}%")
                        ->orWhere('client', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => SiteProject::count(),
            'active' => SiteProject::where('is_active', true)->count(),
        ];

        return view('admin.site_projects.index', compact('projects', 'stats', 'q'));
    }

    // Optional DataTables endpoint
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = SiteProject::query()
            ->withCount(['achievements', 'sliders'])
            ->select(['id', 'name', 'is_active', 'client', 'category', 'created_at']);

        $module = $this->moduleName; // 'site_projects'

        return DataTables::of($q)
            ->addIndexColumn()
            ->editColumn('is_active', fn($r) => $r->is_active
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->editColumn('created_at', fn($r) => $r->created_at?->format('Y-m-d H:i'))
            ->addColumn('actions', function ($r) use ($module) {
                $u = auth()->user();
                $btns = '';

                if ($u->hasPermission("edit_{$module}")) {
                    $btns .= "<a href='" . route("voyager.$module.edit", $r->id) .
                        "' class='btn btn-sm btn-warning me-1'>Edit</a>";
                }
                if ($u->hasPermission("delete_{$module}")) {
                    $btns .= "<a href='javascript:void(0);' data-url='" .
                        route("voyager.$module.destroy", $r->id) .
                        "' data-id='{$r->id}' class='delete-record btn btn-sm btn-danger'>Delete</a>";
                }

                return $btns ?: '—';
            })
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    // Voyager: GET voyager.site_projects.create
    public function create()
    {
        $this->checkPermission('add');

        $project = new SiteProject();

        return view('admin.site_projects.create', compact('project'));
    }

    // Voyager: POST voyager.site_projects.store
    //
    // Fields:
    // - name, image, short_description, full_description, client, duration, category, is_active
    //
    // Achievements:
    // - achievements[n][description], achievements[n][is_active]
    //
    // Sliders (new):
    // - new_sliders[] (files)
    // - new_sliders_captions[] (strings)
    // - new_sliders_is_active[] (checkboxes)
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:4096',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'client' => 'nullable|string|max:190',
            'duration' => 'nullable|string|max:190',
            'category' => 'nullable|string|max:190',
            'is_active' => 'nullable|boolean',

            'achievements' => 'nullable|array',
            'achievements.*.description' => 'nullable|string',
            'achievements.*.is_active' => 'nullable|boolean',

            'new_sliders' => 'nullable|array',
            'new_sliders.*' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:4096',
            'new_sliders_captions' => 'nullable|array',
            'new_sliders_captions.*' => 'nullable|string|max:255',
            'new_sliders_is_active' => 'nullable|array',
            'new_sliders_is_active.*' => 'nullable|boolean',
        ]);

        $data = $validated;
        unset(
            $data['image'],
            $data['achievements'],
            $data['new_sliders'],
            $data['new_sliders_captions'],
            $data['new_sliders_is_active']
        );

        $data['is_active'] = $request->boolean('is_active');

        // Main project image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            list($_, $data['image']) = (new UploaderService())->uploadFile($file, 'site_projects');
        }

        $project = SiteProject::create($data);

        // Achievements
        $achievements = $request->input('achievements', []);
        foreach ($achievements as $row) {
            $desc = trim($row['description'] ?? '');
            if ($desc === '') {
                continue;
            }

            $project->achievements()->create([
                'description' => $desc,
                'is_active' => !empty($row['is_active']),
            ]);
        }

        // New sliders (image + caption + active)
        $newSliderFiles = $request->file('new_sliders', []);
        $newSliderCaptions = $request->input('new_sliders_captions', []);
        $newSliderActives = $request->input('new_sliders_is_active', []);

        if (!empty($newSliderFiles)) {
            foreach ($newSliderFiles as $i => $file) {
                if (!$file) {
                    continue;
                }

                list($_, $path) = (new UploaderService())->uploadFile($file, 'site_projects/sliders');

                $project->sliders()->create([
                    'image' => $path,
                    'caption' => $newSliderCaptions[$i] ?? null,
                    'is_active' => !empty($newSliderActives[$i]),
                ]);
            }
        }

        return redirect()->route('voyager.site_projects.index')
            ->with(['message' => 'Project created successfully', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.site_projects.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $project = SiteProject::with([
            'achievements' => fn($q) => $q->orderBy('id'),
            'sliders' => fn($q) => $q->orderBy('id'),
        ])->findOrFail($id);

        return view('admin.site_projects.edit', compact('project'));
    }

    // Voyager: PUT/PATCH voyager.site_projects.update
    //
    // Existing achievements:
    // - achievements[n][id], [description], [is_active], [delete]
    //
    // Existing sliders:
    // - sliders[n][id], [caption], [is_active], [delete]
    //
    // New sliders:
    // - new_sliders[] (files)
    // - new_sliders_captions[] (strings)
    // - new_sliders_is_active[] (checkboxes)
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $project = SiteProject::with(['achievements', 'sliders'])->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:4096',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'client' => 'nullable|string|max:190',
            'duration' => 'nullable|string|max:190',
            'category' => 'nullable|string|max:190',
            'is_active' => 'nullable|boolean',

            'achievements' => 'nullable|array',
            'achievements.*.id' => 'nullable|integer|exists:site_project_achievements,id',
            'achievements.*.description' => 'nullable|string',
            'achievements.*.is_active' => 'nullable|boolean',
            'achievements.*.delete' => 'nullable|boolean',

            'sliders' => 'nullable|array',
            'sliders.*.id' => 'nullable|integer|exists:site_project_sliders,id',
            'sliders.*.caption' => 'nullable|string|max:255',
            'sliders.*.is_active' => 'nullable|boolean',
            'sliders.*.delete' => 'nullable|boolean',

            'new_sliders' => 'nullable|array',
            'new_sliders.*' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:4096',
            'new_sliders_captions' => 'nullable|array',
            'new_sliders_captions.*' => 'nullable|string|max:255',
            'new_sliders_is_active' => 'nullable|array',
            'new_sliders_is_active.*' => 'nullable|boolean',
        ]);

        $data = $validated;
        unset(
            $data['image'],
            $data['achievements'],
            $data['sliders'],
            $data['new_sliders'],
            $data['new_sliders_captions'],
            $data['new_sliders_is_active']
        );

        $data['is_active'] = $request->boolean('is_active');

        // Replace main image if new uploaded
        if ($request->hasFile('image')) {
            if ($project->image) {
//                UploaderService::deleteFile($project->image);
            }

            $folderName = "site_projects";
            list($_, $data['image']) = (new UploaderService())->uploadFile($request->file('image'), $folderName);
        }

        $project->update($data);

        // === Achievements ===
        $achievements = $request->input('achievements', []);

        foreach ($achievements as $row) {
            $idRow = $row['id'] ?? null;
            $delete = !empty($row['delete']);
            $desc = trim($row['description'] ?? '');
            $active = !empty($row['is_active']);

            if ($idRow) {
                /** @var SiteProjectAchievement|null $ach */
                $ach = $project->achievements->firstWhere('id', (int)$idRow);
                if (!$ach) {
                    continue;
                }

                if ($delete) {
                    $ach->delete();
                    continue;
                }

                $ach->update([
                    'description' => $desc,
                    'is_active' => $active,
                ]);
            } else {
                if ($delete || $desc === '') {
                    continue;
                }

                $project->achievements()->create([
                    'description' => $desc,
                    'is_active' => $active,
                ]);
            }
        }

        // === Existing Sliders (caption + active + delete) ===
        $sliders = $request->input('sliders', []);

        foreach ($sliders as $row) {
            $idRow = $row['id'] ?? null;
            if (!$idRow) {
                continue;
            }

            /** @var SiteProjectSlider|null $slider */
            $slider = $project->sliders->firstWhere('id', (int)$idRow);
            if (!$slider) {
                continue;
            }

            $delete = !empty($row['delete']);
            $active = !empty($row['is_active']);
            $caption = $row['caption'] ?? null;

            if ($delete) {
                if ($slider->image) {
//                    UploaderService::deleteFile($slider->image);
                }
                $slider->delete();
                continue;
            }

            $slider->update([
                'caption' => $caption,
                'is_active' => $active,
            ]);
        }

        // === New Slider Images ===
        $newSliderFiles = $request->file('new_sliders', []);
        $newSliderCaptions = $request->input('new_sliders_captions', []);
        $newSliderActives = $request->input('new_sliders_is_active', []);

        if (!empty($newSliderFiles)) {
            foreach ($newSliderFiles as $i => $file) {
                if (!$file) {
                    continue;
                }

                $folderName = "site_projects/sliders";
                list($_, $path) = (new UploaderService())->uploadImage($file, $folderName);

                $project->sliders()->create([
                    'image' => $path,
                    'caption' => $newSliderCaptions[$i] ?? null,
                    'is_active' => !empty($newSliderActives[$i]),
                ]);
            }
        }

        return redirect()->route('voyager.site_projects.index')
            ->with(['message' => 'Project updated successfully', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.site_projects.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $project = SiteProject::with(['sliders'])->findOrFail($id);

        // Delete main image
        if ($project->image) {
            UploaderService::deleteFile($project->image);
        }

        // Delete slider images
        foreach ($project->sliders as $slider) {
            if ($slider->image) {
                UploaderService::deleteFile($slider->image);
            }
        }

        // Achievements & sliders rows removed via DB cascade
        $project->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}
