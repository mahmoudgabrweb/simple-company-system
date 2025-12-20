<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteClient;
use App\Services\UploaderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SiteClientController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'site_clients';
        $this->model = SiteClient::class;
    }

    // Voyager: GET voyager.site_services.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $clients = SiteClient::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%");
                });
            })
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => SiteClient::count(),
            'active' => SiteClient::where('is_active', true)->count(),
        ];

        return view('admin.site_clients.index', compact('clients', 'stats', 'q'));
    }

    // Voyager: GET voyager.site_clients.create
    public function create()
    {
        $this->checkPermission('add');

        $service = new SiteClient();

        return view('admin.site_clients.create', compact('service'));
    }

    // Voyager: POST voyager.site_clients.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4000',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Prepare data without icon first
        $data = $validated;

        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;

        $folder = "site_clients";
        if ($request->hasFile('image')) {
            [$ok, $path] = (new UploaderService())->uploadFile($request->file('image'), $folder);
            if (!$ok) return back()->withErrors(['image' => 'Image upload failed'])->withInput();
            $data['image'] = $path;
        }

        SiteClient::create($data);

        return redirect()->route('voyager.site_clients.index')
            ->with(['message' => 'Service created successfully', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.site_clients.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $client = SiteClient::findOrFail($id);

        return view('admin.site_clients.edit', compact('client'));
    }

    // Voyager: PUT/PATCH voyager.site_clients.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $service = SiteClient::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4000',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? $service->display_order;

        $folder = 'site_clients';
        if ($request->hasFile('image')) {
            [$_, $path] = (new UploaderService())->uploadFile($request->file('image'), $folder);
            $data['image'] = $path;
        }

        $service->update($data);

        return redirect()->route('voyager.site_clients.index')
            ->with(['message' => 'Service updated successfully', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.site_clients.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $service = SiteClient::findOrFail($id);

        $service->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}
