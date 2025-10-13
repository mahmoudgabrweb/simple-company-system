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
                            <span class="me-2">Client:  <strong>{{ $project->client->name }}</strong></span>
                        @endif
                        @if(!empty($project->code))
                            <span class="me-2">Code: <strong>{{ $project->code }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('voyager.projects.edit', $project->id) }}" class="btn btn-outline-secondary">Edit
                    Project</a>
                @if($activeQuotation)
                    <a href="{{ route('voyager.projects.variations.index', $project->id) }}" class="btn btn-primary">
                        Variations
                    </a>
                @else
                    <button class="btn btn-primary" disabled title="Add a quotation first">Variations</button>
                @endif
                <a href="{{ route('voyager.projects.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Status</div>
                        <div class="d-flex align-items-center justify-content-between">
            <span class="badge text-bg-{{ ($project->status ?? '') === 'active' ? 'success' : 'secondary' }}">
              {{ ucfirst($project->status ?? '—') }}
            </span>
                            @if(!empty($project->start_date))
                                <span class="small text-muted">Start: {{ \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d') }}</span>
                            @endif
                        </div>
                        @if(!empty($project->end_date))
                            <div class="small text-muted mt-1">
                                End: {{ \Illuminate\Support\Carbon::parse($project->end_date)->format('Y-m-d') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Active Quotation</div>
                        @if($activeQuotation)
                            <div class="fw-semibold">#{{ $activeQuotation->id }}</div>
                            <div class="small">Status:
                                <span class="badge text-bg-{{ $activeQuotation->status==='accepted'?'success':($activeQuotation->status==='sent'?'info':'secondary') }}">
                {{ ucfirst($activeQuotation->status) }}
              </span>
                            </div>
                            <div class="mt-1 small">
                                Total:
                                <strong>{{ number_format($activeQuotation->total ?? 0, 2) }} {{ $activeQuotation->currency ?? 'AED' }}</strong>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('voyager.quotations.edit', $activeQuotation->id) }}"
                                   class="btn btn-sm btn-outline-primary">Open Quotation</a>
                            </div>
                        @else
                            <div class="text-muted">No active quotation. Add one to enable variations.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Payments</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($paymentsSummary['total'] ?? 0, 2) }} AED
                        </div>
                        <div class="small text-muted">Transactions: {{ $paymentsSummary['count'] ?? 0 }}</div>
                        @if(!empty($paymentsSummary['latest']))
                            <div class="small mt-1">
                                Latest: {{ \Illuminate\Support\Carbon::parse($paymentsSummary['latest']->paid_at)->format('Y-m-d') }}</div>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('voyager.payments.index', ['project_id' => $project->id]) }}"
                               class="btn btn-sm btn-outline-secondary">View Payments</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Budget</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($project->budget ?? 0, 2) }} {{ $project->currency ?? 'AED' }}
                        </div>
                        <div class="small text-muted">Owner: {{ $project->owner_name ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details + Notes --}}
        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <strong>Project Details</strong>
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
                                <div class="text-muted small">Location</div>
                                <div class="fw-semibold">{{ $project->location ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Type</div>
                                <div class="fw-semibold">{{ $project->type ?? '—' }}</div>
                            </div>
                        </div>
                        @if(!empty($project->description))
                            <hr>
                            <div class="text-muted small mb-1">Description</div>
                            <div class="fw-normal">{{ $project->description }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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
                                   href="{{ route('voyager.projects.variations.edit', [$project->id, $v->id]) }}">
                                    <div>
                                        <div class="fw-semibold">{{ $v->title ?? ('Variation #'.$v->id) }}</div>
                                        <div class="small text-muted">
                                            {{ ucfirst($v->status) }} • {{ $v->created_at->format('Y-m-d') }}
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
                            <small class="text-muted">Variations allowed while quotation is active (not
                                completed).</small>
                        </div>
                    @endif
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
                @if($activeQuotation)
                    <a href="{{ route('voyager.projects.variations.create', $project->id) }}"
                       class="btn btn-outline-primary">
                        New Variation
                    </a>
                @endif
                <a href="{{ route('voyager.payments.index', ['project_id' => $project->id]) }}"
                   class="btn btn-outline-secondary">
                    Project Payments
                </a>
            </div>
        </div>

    </div>
@endsection
