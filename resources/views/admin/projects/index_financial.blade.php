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
                           placeholder="Project name / Address">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100">Search</button>
                </div>
            </div>
        </form>

        @if($projects->count() === 0)
            <div class="card p-4 text-center text-muted">No projects.</div>
        @else
            {{-- Project rows styled like your expenses page: table + per-project cards --}}
            @foreach($projects as $p)
                @php
                    $f = $finance[$p->id] ?? ['total'=>0,'paid'=>0,'exp'=>0,'remaining'=>0];
                @endphp
                <div class="card p-3 mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="m-0">{{ $p->name }}</h5>
                            <div class="text-muted small">
                                Client: {{ $p->client->name ?? '—' }} · City: {{ $p->city->name ?? '—' }} ·
                                Address: {{ $p->address ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('voyager.projects.financials', $p->id) }}"
                               class="btn btn-outline-primary">
                                Payments & Expenses Details
                            </a>
                            <a href="{{ route('voyager.projects.edit', $p->id) }}" class="btn btn-warning">Edit</a>
                        </div>
                    </div>

                    {{-- Cards: total, paid, expenses, remaining --}}
                    <div class="row g-2 mt-2">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Active Quotation Total</div>
                                <div class="fs-5 fw-bold">{{ number_format($f['total'],2) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Total Paid</div>
                                <div class="fs-5 fw-bold">{{ number_format($f['paid'],2) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Project Expenses Total</div>
                                <div class="fs-5 fw-bold">{{ number_format($f['exp'],2) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Remaining</div>
                                <div class="fs-5 fw-bold {{ $f['remaining'] < 0 ? 'text-danger' : '' }}">
                                    {{ number_format($f['remaining'],2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-2">{{ $projects->links() }}</div>
        @endif
    </div>
@endsection
