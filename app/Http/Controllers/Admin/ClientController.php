<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\City;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'clients';
        $this->model = Client::class;
    }

    // Voyager: GET voyager.clients.index
    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = trim($request->get('q', ''));

        $clients = Client::query()
            ->with(['city' => fn($q) => $q->select('id', 'name')]) // if relation exists
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('alternative_phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Client::count(),
        ];

        return view('admin.clients.index', compact('clients', 'stats', 'q'));
    }

    // Custom: GET /admin/clients/load
    public function load(Request $request): JsonResponse
    {
        $this->checkPermission('browse');

        $q = Client::query()
            ->with('city:id,name')
            ->select(['id', 'name', 'phone', 'alternative_phone', 'email', 'city_id', 'address', 'created_at']);

        $module = $this->moduleName; // 'clients'

        return DataTables::of($q)
            ->addIndexColumn()
            ->addColumn('city', fn($r) => $r->city?->name ?? '—')
            ->editColumn('address', function ($r) {
                $txt = (string)($r->address ?? '');
                $txt = trim(strip_tags($txt));
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
            ->make(true);
    }

    // Voyager: GET voyager.clients.create
    public function create()
    {
        $this->checkPermission('add');

        $client = new Client();
        // Cities are company-scoped via BelongsToCompany global scope
        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('admin.clients.create', compact('client', 'cities'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: POST voyager.clients.store
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->checkPermission('add');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => 'required|string|max:190',
            'phone' => [
                'required', 'string', 'max:50',
                Rule::unique('clients', 'phone')->where(fn($q) => $q->where('company_id', $cid)),
            ],
            'alternative_phone' => 'nullable|string|max:50',
            'email' => [
                'nullable', 'email', 'max:190',
                Rule::unique('clients', 'email')->where(fn($q) => $q->where('company_id', $cid)),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'map' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        Client::create($request->only([
            'name', 'phone', 'alternative_phone', 'email', 'city_id', 'map', 'address'
        ]));

        return redirect()->route('voyager.clients.index')
            ->with(['message' => 'تم إنشاء العميل بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: GET voyager.clients.edit
    public function edit(int $id)
    {
        $this->checkPermission('edit');

        $client = Client::findOrFail($id);
        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('admin.clients.edit', compact('client', 'cities'))
            ->with('currentCompany', CompanyContext::company());
    }

    // Voyager: PUT/PATCH voyager.clients.update
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->checkPermission('edit');

        $cid = CompanyContext::id();

        $request->validate([
            'name' => 'required|string|max:190',
            'phone' => [
                'required', 'string', 'max:50',
                Rule::unique('clients', 'phone')
                    ->where(fn($q) => $q->where('company_id', $cid))
                    ->ignore($id),
            ],
            'alternative_phone' => 'nullable|string|max:50',
            'email' => [
                'nullable', 'email', 'max:190',
                Rule::unique('clients', 'email')
                    ->where(fn($q) => $q->where('company_id', $cid))
                    ->ignore($id),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'map' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $client = Client::findOrFail($id);
        $client->update($request->only([
            'name', 'phone', 'alternative_phone', 'email', 'city_id', 'map', 'address'
        ]));

        return redirect()->route('voyager.clients.index')
            ->with(['message' => 'تم تحديث العميل بنجاح', 'alert-type' => 'success']);
    }

    // Voyager: DELETE voyager.clients.destroy
    public function destroy(int $id): JsonResponse
    {
        $this->checkPermission('delete');

        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف بنجاح']);
    }
}
