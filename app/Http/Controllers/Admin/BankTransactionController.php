<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransaction;
use App\Models\SupplierMaterial;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankTransactionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('browse', app(\App\Models\BankTransaction::class));

        $cid = \App\Support\CompanyContext::id();
        $type = $request->get('type');
        $from = $request->get('from');
        $to = $request->get('to');

        $transactions = \App\Models\BankTransaction::where('company_id', $cid)
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($from, fn($q) => $q->whereDate('txn_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('txn_date', '<=', $to))
            ->orderByDesc('txn_date')->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bank_transactions.index', compact('transactions', 'type', 'from', 'to'))
            ->with('currentCompany', \App\Support\CompanyContext::company());
    }

    public function create()
    {
        $this->authorize('add', app(BankTransaction::class));

        return view('admin.bank_transactions.create')
            ->with('currentCompany', CompanyContext::company());
    }

    public function store(Request $request)
    {
        $this->authorize('add', app(BankTransaction::class));

        $data = $request->validate([
            'txn_date' => ['required', 'date'],
            'type' => ['required', 'in:deposit,withdrawal,transfer_in,transfer_out,fee,interest'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'account_label' => ['nullable', 'string', 'max:100'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'counterparty' => ['nullable', 'string', 'max:150'],
            'reference' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'reconciled' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $cid = CompanyContext::id();

        DB::transaction(function () use ($request, $data, $cid) {
            $payload = array_merge($data, [
                'company_id' => $cid,
                'created_by' => auth()->id(),
                'reconciled' => (bool)($data['reconciled'] ?? false),
            ]);

            if ($request->hasFile('attachment')) {
                $payload['attachment_path'] = $request->file('attachment')->store('bank_transactions', 'public');
            }

            BankTransaction::create($payload);
        });

        return redirect()->route('voyager.bank_transactions.index')
            ->with('success', 'Transaction has been added successfully.');
    }

    public function edit(int $id)
    {
        $this->authorize('edit', app(BankTransaction::class));

        $txn = BankTransaction::where('company_id', CompanyContext::id())->findOrFail($id);

        return view('admin.bank_transactions.edit', compact('txn'))
            ->with('currentCompany', CompanyContext::company());
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('edit', app(BankTransaction::class));

        $data = $request->validate([
            'txn_date' => ['required', 'date'],
            'type' => ['required', 'in:deposit,withdrawal,transfer_in,transfer_out,fee,interest'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'account_label' => ['nullable', 'string', 'max:100'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'counterparty' => ['nullable', 'string', 'max:150'],
            'reference' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'reconciled' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:8192'],
        ]);

        DB::transaction(function () use ($request, $id, $data) {
            $txn = BankTransaction::where('company_id', CompanyContext::id())->findOrFail($id);

            $payload = array_merge($data, [
                'reconciled' => (bool)($data['reconciled'] ?? false),
            ]);

            if ($request->hasFile('attachment')) {
                if ($txn->attachment_path) {
                    Storage::disk('public')->delete($txn->attachment_path);
                }
                $payload['attachment_path'] = $request->file('attachment')->store('bank_transactions', 'public');
            }

            $txn->update($payload);
        });

        return back()->with('success', 'Transaction has been saved successfully.');
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', app(BankTransaction::class));

        $txn = BankTransaction::where('company_id', CompanyContext::id())->findOrFail($id);

        if ($txn->attachment_path) {
            Storage::disk('public')->delete($txn->attachment_path);
        }

        $txn->delete();

        return redirect()->route('voyager.bank_transactions.index')
            ->with('success', 'Transaction has been deleted successfully.');
    }

    public function changeReconciled(Request $request, int $id)
    {
        $this->authorize('edit', app(BankTransaction::class));

        $txn = BankTransaction::where('company_id', CompanyContext::id())->findOrFail($id);

        $data = $request->validate([
            'reconciled' => ['required', 'boolean'],
        ]);

        $txn->reconciled = (bool)$data['reconciled'];
        $txn->save();

        return back()->with('success', 'The status has been updated');
    }

    public function toggleReconciled(int $id)
    {
        $this->authorize('edit', app(BankTransaction::class));

        $txn = BankTransaction::where('company_id', CompanyContext::id())->findOrFail($id);
        $txn->reconciled = !$txn->reconciled;
        $txn->save();

        return response()->json([
            'status' => true,
            'reconciled' => (bool)$txn->reconciled,
            'message' => 'The status has been updated',
        ]);
    }

    public function download(int $id)
    {
        $this->authorize('read', app(SupplierMaterial::class));

        $companyId = CompanyContext::id();
        $material = BankTransaction::when($companyId, fn($x) => $x->where('company_id', $companyId))->findOrFail($id);

        if (!$material->attachment_path || !Storage::disk('public')->exists($material->attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($material->attachment_path);
    }
}
