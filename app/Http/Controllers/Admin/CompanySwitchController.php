<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Support\CompanyContext;
use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    public function select()
    {
        $this->authorize('browse_admin'); // or your own gate
        $companies = Company::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'code']);
        return view('admin.company.select', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate(['company_id' => 'required|exists:companies,id']);
        CompanyContext::set((int)$request->company_id);
        return redirect()->route('voyager.dashboard'); // or your admin home
    }
}
