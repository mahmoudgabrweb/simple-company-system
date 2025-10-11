<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Payment;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectFinanceController extends Controller
{
    protected function activeCompanyId(): ?int
    {
        return CompanyContext::id();
    }

    /**
     * Projects index (cards per project + action to details)
     */
    public function index(Request $request)
    {
        $this->authorize('browse', app(Project::class));

        $companyId = $this->activeCompanyId();

        // Filters (optional): by name or client
        $q = Project::query()
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

        // Paginate projects
        $projects = $q->paginate(15)->appends($request->query());

        // Preload financial aggregates for visible page (efficient)
        $projectIds = $projects->pluck('id');

        // Total amount = active quotation total_amount (if any), else 0
        $activeTotals = Quotation::select('project_id', DB::raw('MAX(total_amount) as total_amount'))
            ->whereIn('project_id', $projectIds)
            ->where('is_active', true)
            ->groupBy('project_id')
            ->pluck('total_amount', 'project_id');

        $paidTotals = Payment::select('project_id', DB::raw('SUM(amount) as total'))
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $expenseTotals = ProjectExpense::select('project_id', DB::raw('SUM(amount) as total'))
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        // Map into an array keyed by project_id
        $finance = [];
        foreach ($projectIds as $pid) {
            $total = (float)($activeTotals[$pid] ?? 0);
            $paid = (float)($paidTotals[$pid] ?? 0);
            $exp = (float)($expenseTotals[$pid] ?? 0);
            $remaining = $total - ($paid + $exp);
            $finance[$pid] = compact('total', 'paid', 'exp', 'remaining');
        }

        return view('admin.projects.index_financial', compact('projects', 'finance'));
    }

    /**
     * Project financial details: payments + expenses + totals
     */
    public function financials(int $projectId, Request $request)
    {
        $this->authorize('read', app(Project::class));

        $companyId = $this->activeCompanyId();

        $project = Project::when($companyId, fn($x) => $x->where('company_id', $companyId))
            ->with(['client', 'city'])
            ->findOrFail($projectId);

        // Totals
        $activeQuotationTotal = Quotation::where('project_id', $project->id)
            ->where('is_active', true)
            ->value('total_amount') ?? 0;

        $totalPaid = Payment::where('project_id', $project->id)->sum('amount');
        $totalExpenses = ProjectExpense::where('project_id', $project->id)->sum('amount');
        $remaining = (float)$activeQuotationTotal - ((float)$totalPaid + (float)$totalExpenses);

        // Lists (with optional filters)
        $payQ = Payment::with('receiver')->where('project_id', $project->id)->orderByDesc('paid_at')->orderByDesc('id');
        if ($request->filled('method')) $payQ->where('payment_method', $request->string('method'));
        if ($request->filled('date_from')) $payQ->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        if ($request->filled('date_to')) $payQ->where('paid_at', '<=', $request->date('date_to')->endOfDay());
        $payments = $payQ->paginate(15, ['*'], 'payments_page')->appends($request->query());

        $expQ = ProjectExpense::with('payer')->where('project_id', $project->id)->orderByDesc('paid_at')->orderByDesc('id');
        if ($request->filled('type')) $expQ->where('payment_type', $request->string('type'));
        if ($request->filled('date_from')) $expQ->where('paid_at', '>=', $request->date('date_from')->startOfDay());
        if ($request->filled('date_to')) $expQ->where('paid_at', '<=', $request->date('date_to')->endOfDay());
        $expenses = $expQ->paginate(15, ['*'], 'expenses_page')->appends($request->query());

        $methodsMap = ['cash' => 'نقدًا', 'transfer' => 'تحويل', 'cheque' => 'شيك', 'card' => 'بطاقة'];

        return view('admin.projects.financials', compact(
            'project', 'activeQuotationTotal', 'totalPaid', 'totalExpenses', 'remaining',
            'payments', 'expenses', 'methodsMap'
        ));
    }
}
