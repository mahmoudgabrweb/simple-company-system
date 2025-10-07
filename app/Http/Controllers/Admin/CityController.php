<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CityController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'cities';
        $this->model = City::class;
    }

    // Voyager: GET voyager.cities.index
    public function index()
    {
        $this->checkPermission('browse');

        $stats = [
            'total' => City::count(),
        ];

        return view('admin.cities.index', compact('stats'));
    }

    // Custom: GET /admin/cities/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = City::query()->select(['id', 'name', 'created_at']);
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

    // Voyager: GET voyager.cities.create
    public function create()
    {
        $this->checkPermission('add');
        $city = new City();
        return view('admin.cities.create', compact('city'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.cities.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();
        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                Rule::unique('cities', 'name')->where(fn($q) => $q->where('company_id', $cid)),
            ],
        ]);

        DB::transaction(function () use ($request) {
            City::create([
                'name' => (string)$request->input('name'),
            ]);
        });

        return redirect()->route('voyager.cities.index')
            ->with(['message' => 'تم إنشاء المدينة بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.cities.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');
        $city = City::findOrFail($id);

        return view('admin.cities.edit', compact('city'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.cities.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();
        $request->validate([
            'name' => [
                'required', 'string', 'max:190',
                Rule::unique('cities', 'name')
                    ->where(fn($q) => $q->where('company_id', $cid))
                    ->ignore($id),
            ],
        ]);

        DB::transaction(function () use ($request, $id) {
            $city = City::findOrFail($id);
            $city->update(['name' => (string)$request->input('name')]);
        });

        return redirect()->route('voyager.cities.index')
            ->with(['message' => 'تم تحديث المدينة بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.cities.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
