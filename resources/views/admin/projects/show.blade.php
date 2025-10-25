@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:#1D2A3A1a;">
                    <span class="fw-bold"
                          style="color:#1D2A3A">{{ strtoupper(substr($project->name ?? 'P',0,1)) }}</span>
                </div>
                <div>
                    <h4 class="m-0">{{ $project->name }}</h4>
                    <div class="text-muted small">
                        @if($project->company)
                            <span class="me-2">Company: <strong>{{ $project->company->name }}</strong></span>
                        @endif
                        @if($project->client)
                            <span class="me-2">Client: <strong>{{ $project->client->name }}</strong></span>
                        @endif
                        @if($project->city)
                            <span class="me-2">City: <strong>{{ $project->city->name }}</strong></span>
                        @endif
                        @if(!empty($project->address))
                            <span class="me-2">Address: <strong>{{ $project->address }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('voyager.projects.edit', $project->id) }}" class="btn btn-outline-secondary">
                    Edit Project
                </a>

                {{-- Variations entry point (only if there is an active quotation and it's NOT completed) --}}
                @php
                    $canDoVariations = $activeQuotation && ($activeQuotation->status !== 'completed');
                @endphp
                @if($canDoVariations)
                    <a href="{{ route('voyager.projects.variations.index', $project->id) }}" class="btn btn-primary">
                        Variations
                    </a>
                @else
                    <button class="btn btn-primary" disabled
                            title="{{ $activeQuotation ? 'Quotation is completed' : 'Add a quotation first' }}">
                        Variations
                    </button>
                @endif

                <a href="{{ route('voyager.projects.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        {{-- Big Stats Row --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Contract (Active Quotation)</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($contractBase, 2) }} AED
                        </div>
                        <div class="small mt-1">
                            @if($activeQuotation)
                                <span class="text-muted">Status:</span>
                                <span class="badge text-bg-{{ $activeQuotation->status === 'approved' ? 'success' : ($activeQuotation->status === 'sent' ? 'info' : 'secondary') }}">
                                {{ ucfirst(str_replace('_',' ', $activeQuotation->status)) }}
                            </span>
                                <a class="btn btn-sm btn-outline-primary ms-2"
                                   href="{{ route('voyager.quotations.edit', $activeQuotation->id) }}">
                                    Open
                                </a>
                            @else
                                <span class="text-muted">No active quotation</span>
                                <a class="btn btn-sm btn-outline-secondary ms-2"
                                   href="{{ route('voyager.quotations.index', ['project_id' => $project->id]) }}">
                                    Manage
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Variations (accepted/completed included in contract+) --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Accepted Variations Total</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format(($variationTotalsByStatus['accepted']->total ?? 0) + ($variationTotalsByStatus['completed']->total ?? 0), 2) }}
                            AED
                        </div>
                        <div class="small text-muted">
                            Status counts:
                            <span class="ms-1">Draft: {{ $variationTotalsByStatus['draft']->cnt ?? 0 }}</span> •
                            <span>Sent: {{ $variationTotalsByStatus['sent']->cnt ?? 0 }}</span> •
                            <span>Accepted: {{ $variationTotalsByStatus['accepted']->cnt ?? 0 }}</span> •
                            <span>Completed: {{ $variationTotalsByStatus['completed']->cnt ?? 0 }}</span>
                        </div>
                        <div class="mt-2">
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('voyager.projects.variations.index', $project->id) }}">Manage
                                Variations</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payments received --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Payments Received</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($receivedTotal, 2) }} AED
                        </div>
                        <div class="small text-muted">Transactions: {{ $paymentsSummary['count'] ?? 0 }}</div>
                        @if(!empty($paymentsSummary['latest']))
                            <div class="small text-muted">
                                Latest: {{ \Illuminate\Support\Carbon::parse($paymentsSummary['latest']->paid_at)->format('Y-m-d') }}</div>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('voyager.payments.index', ['project_id' => $project->id]) }}"
                               class="btn btn-sm btn-outline-secondary">View Payments</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Receivable (Contract + Accepted Variations - Received) --}}
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Remaining Receivable</div>
                        <div class="display-6 fw-bold mb-0 {{ $remainingReceivable < 0 ? 'text-danger' : '' }}"
                             style="font-size:1.35rem;">
                            {{ number_format($remainingReceivable, 2) }} AED
                        </div>
                        <div class="small text-muted">
                            Contract+: {{ number_format($contractPlusVariations,2) }} AED
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cost Buckets --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Project Expenses</div>
                        <div class="fs-5 fw-bold">{{ number_format($projectExpensesTotal, 2) }} AED</div>
                        <a href="{{ route('voyager.project_expenses.index', ['project_id' => $project->id]) }}"
                           class="btn btn-sm btn-outline-secondary mt-2">Open</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Supplier Materials</div>
                        <div class="fs-5 fw-bold">{{ number_format($supplierMaterialsTotal, 2) }} AED</div>
                        <a href="{{ route('voyager.supplier_materials.index', ['project_id' => $project->id]) }}"
                           class="btn btn-sm btn-outline-secondary mt-2">Open</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Supplier Payments (linked)</div>
                        <div class="fs-5 fw-bold">{{ number_format($supplierPaymentsTotal, 2) }} AED</div>
                        <a href="{{ route('voyager.supplier_payments.index', ['project_id' => $project->id]) }}"
                           class="btn btn-sm btn-outline-secondary mt-2">Open</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Salaries</div>
                        <div class="fs-5 fw-bold">{{ number_format($salariesTotal, 2) }} AED</div>
                        <a href="{{ route('voyager.salaries.index', ['project_id' => $project->id]) }}"
                           class="btn btn-sm btn-outline-secondary mt-2">Open</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details + Variations quick list --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Project Details</strong>
                        <div class="text-muted small">Direct Costs: <strong>{{ number_format($directCosts,2) }}
                                AED</strong></div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Company</div>
                                <div class="fw-semibold">{{ $project->company->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Client</div>
                                <div class="fw-semibold">{{ $project->client->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">City</div>
                                <div class="fw-semibold">{{ $project->city->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Address</div>
                                <div class="fw-semibold">{{ $project->address ?? '—' }}</div>
                            </div>
                            @if(!empty($project->location))
                                <div class="col-12">
                                    <div class="text-muted small">Location</div>
                                    <div class="fw-semibold">{{ $project->location }}</div>
                                </div>
                            @endif
                            @if(!empty($project->map))
                                <div class="col-12">
                                    <div class="text-muted small">Map</div>
                                    <div class="fw-normal">{{ $project->map }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quotations table --}}
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Quotations</strong>
                            <a class="btn btn-sm btn-outline-secondary"
                               href="{{ route('voyager.quotations.index', ['project_id' => $project->id]) }}">
                                Manage
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Active</th>
                                    <th class="text-end">Total (AED)</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($quotations as $q)
                                    <tr>
                                        <td>{{ $q->quotation_number ?? $q->id }}</td>
                                        <td>
                                            <span class="badge text-bg-{{ $q->status === 'approved' ? 'success' : ($q->status === 'sent' ? 'info' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_',' ', $q->status)) }}
                                            </span>
                                        </td>
                                        <td>{!! $q->is_active ? '<span class="badge text-bg-primary">Active</span>' : '—' !!}</td>
                                        <td class="text-end">{{ number_format($q->total_amount, 2) }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="{{ route('voyager.quotations.edit', $q->id) }}">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">No quotations.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Variations card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Variations</strong>
                        <a href="{{ route('voyager.projects.variations.index', $project->id) }}"
                           class="btn btn-sm btn-outline-primary">Manage</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentVariations as $v)
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                   href="{{ route('voyager.projects.variations.edit',[$project->id,$v->id]) }}">
                                    <div>
                                        <div class="fw-semibold">{{ $v->title ?? ('Variation #'.$v->id) }}</div>
                                        <div class="small text-muted">
                                            {{ ucfirst($v->status) }} • {{ optional($v->created_at)->format('Y-m-d') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{ number_format($v->total, 2) }} {{ $v->currency }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-muted">No variations yet.</div>
                            @endforelse
                        </div>
                    </div>
                    @if($activeQuotation)
                        <div class="card-footer bg-white">
                            <small class="text-muted">
                                Variations allowed while active quotation is not completed.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Milestones --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Milestones</strong>
                <a href="{{ route('voyager.milestones.index', ['project_id' => $project->id]) }}"
                   class="btn btn-sm btn-outline-secondary">Manage</a>
            </div>
            <div class="card-body">
                @php
                    $msPlanned  = $milestoneTotalsByStatus['planned']['amount']  ?? 0;
                    $msApproved = $milestoneTotalsByStatus['approved']['amount'] ?? 0;
                    $msInvoiced = $milestoneTotalsByStatus['invoiced']['amount'] ?? 0;
                    $msPaid     = $milestoneTotalsByStatus['paid']['amount']     ?? 0;
                @endphp
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 h-100">
                            <div class="text-muted small">Planned</div>
                            <div class="fw-bold">{{ number_format($msPlanned, 2) }} AED</div>
                            <div class="small text-muted">
                                Count: {{ $milestoneTotalsByStatus['planned']['count'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 h-100">
                            <div class="text-muted small">Approved</div>
                            <div class="fw-bold">{{ number_format($msApproved, 2) }} AED</div>
                            <div class="small text-muted">
                                Count: {{ $milestoneTotalsByStatus['approved']['count'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 h-100">
                            <div class="text-muted small">Invoiced</div>
                            <div class="fw-bold">{{ number_format($msInvoiced, 2) }} AED</div>
                            <div class="small text-muted">
                                Count: {{ $milestoneTotalsByStatus['invoiced']['count'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 h-100">
                            <div class="text-muted small">Paid</div>
                            <div class="fw-bold">{{ number_format($msPaid, 2) }} AED</div>
                            <div class="small text-muted">
                                Count: {{ $milestoneTotalsByStatus['paid']['count'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('voyager.quotations.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    View All Quotations
                </a>
                @if($canDoVariations)
                    <a href="{{ route('voyager.projects.variations.index',$project->id) }}"
                       class="btn btn-outline-primary">
                        New / Manage Variations
                    </a>
                @endif
                <a href="{{ route('voyager.payments.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Project Payments
                </a>
                <a href="{{ route('voyager.project_expenses.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Project Expenses
                </a>
                <a href="{{ route('voyager.salaries.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Project Salaries
                </a>
                <a href="{{ route('voyager.supplier_materials.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Supplier Materials
                </a>
                <a href="{{ route('voyager.supplier_payments.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Supplier Payments
                </a>
                <a href="{{ route('voyager.milestones.index', ['project_id' => $project->id]) }}" class="btn btn-outline-secondary">
                    Milestones
                </a>
            </div>
        </div>

    </div>
@endsection
