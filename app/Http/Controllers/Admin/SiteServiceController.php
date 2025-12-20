<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SiteServiceController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'site_services';
        $this->model = SiteService::class;
    }

    // Voyager: GET voyager.site_services.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $services = SiteService::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', "%{$q}%")
                        ->orWhere('short_description', 'like', "%{$q}%");
                });
            })
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => SiteService::count(),
            'active' => SiteService::where('is_active', true)->count(),
        ];

        return view('admin.site_services.index', compact('services', 'stats', 'q'));
    }

    // Voyager: GET voyager.site_services.create
    public function create()
    {
        $this->checkPermission('add');

        $service = new SiteService();

        return view('admin.site_services.create', compact('service'));
    }

    // Voyager: POST voyager.site_services.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $validated = $request->validate([
            'title' => 'required|string|max:190',
            'icon' => 'required|string|max:190',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Prepare data without icon first
        $data = $validated;

        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;

        SiteService::create($data);

        return redirect()->route('voyager.site_services.index')
            ->with(['message' => 'Service created successfully', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.site_services.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $service = SiteService::findOrFail($id);

        return view('admin.site_services.edit', compact('service'));
    }

    // Voyager: PUT/PATCH voyager.site_services.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $service = SiteService::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:190',
            'icon' => 'required|string|max:190',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? $service->display_order;

        $service->update($data);

        return redirect()->route('voyager.site_services.index')
            ->with(['message' => 'Service updated successfully', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.site_services.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $service = SiteService::findOrFail($id);

        $service->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}
