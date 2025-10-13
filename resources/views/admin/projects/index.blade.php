@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Projects</h4>
            <a href="{{ route('voyager.projects.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Project
            </a>
        </div>

        {{-- Simple search --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Project name / address">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100">Search</button>
                </div>
            </div>
        </form>

        @if($projects->count() === 0)
            <div class="card p-4 text-center text-muted">No projects found.</div>
        @else
            @foreach($projects as $p)
                @php
                    $f = $finance[$p->id] ?? [];
                    $totalQuotation = (float)($f['totalQuotation'] ?? 0);
                    $paid           = (float)($f['paid'] ?? 0);
                    $exp            = (float)($f['exp'] ?? 0);
                    $varsAll        = (float)($f['varsAll'] ?? 0);
                    $varsAccepted   = (float)($f['varsAccepted'] ?? 0);
                    $remaining      = (float)($f['remaining'] ?? (($totalQuotation + $varsAccepted) - $paid));
                @endphp

                <div class="card p-3 mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="m-0">{{ $p->name }}</h5>
                            <div class="text-muted small">
                                Client: {{ $p->client->name ?? '—' }}
                                · City: {{ $p->city->name ?? '—' }}
                                · Address: {{ $p->address ?? '—' }}
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            {{-- Show (project details) --}}
                            <a href="{{ route('voyager.projects.show', $p->id) }}" class="btn btn-outline-secondary">
                                Show
                            </a>

                            {{-- Variations (module index for this project) --}}
                            <a href="{{ route('voyager.projects.variations.index', $p->id) }}"
                               class="btn btn-outline-primary">
                                Variations
                            </a>

                            <a href="{{ route('voyager.projects.financials', $p->id) }}"
                               class="btn btn-outline-primary">
                                Payments & Expenses
                            </a>
                            <a href="{{ route('voyager.projects.edit', $p->id) }}" class="btn btn-warning">Edit</a>
                        </div>
                    </div>

                    {{-- Cards: Active Quotation Total / Variations (All) / Variations (Accepted) / Paid / Project Expenses / Outstanding --}}
                    <div class="row g-2 mt-2">
                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Active Quotation Total</div>
                                <div class="fs-5 fw-bold">{{ number_format($totalQuotation, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Variations (All)</div>
                                <div class="fs-5 fw-bold">{{ number_format($varsAll, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Variations (Accepted)</div>
                                <div class="fs-5 fw-bold">{{ number_format($varsAccepted, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Total Paid</div>
                                <div class="fs-5 fw-bold">{{ number_format($paid, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Project Expenses</div>
                                <div class="fs-5 fw-bold">{{ number_format($exp, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Outstanding*</div>
                                <div class="fs-5 fw-bold {{ $remaining < 0 ? 'text-danger' : '' }}">
                                    {{ number_format($remaining, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted mt-1">
                        * Outstanding = Active Quotation Total + Accepted Variations − Total Paid.
                    </div>
                </div>
            @endforeach

            <div class="mt-2">{{ $projects->links() }}</div>
        @endif
    </div>
@endsection
