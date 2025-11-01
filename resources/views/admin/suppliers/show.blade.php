@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:#1D2A3A1a;">
        <span class="fw-bold" style="color:#1D2A3A">
          {{ strtoupper(substr($supplier->name ?? 'S',0,1)) }}
        </span>
                </div>
                <div>
                    <h4 class="m-0">{{ $supplier->name }}</h4>
                    <div class="text-muted small">
                        @if($supplier->company)
                            <span class="me-2">Company: <strong>{{ $supplier->company->name }}</strong></span>
                        @endif
                        @if($supplier->city)
                            <span class="me-2">City: <strong>{{ $supplier->city->name }}</strong></span>
                        @endif
                        @if(!empty($supplier->address))
                            <span class="me-2">Address: <strong>{{ $supplier->address }}</strong></span>
                        @endif
                        @if(!empty($supplier->contact_person_name))
                            <span class="me-2">Contact: <strong>{{ $supplier->contact_person_name }}</strong></span>
                        @endif
                        @if(!empty($supplier->contact_person_phone))
                            <span class="me-2">Phone: <strong>{{ $supplier->contact_person_phone }}</strong></span>
                        @endif
                        @if(!empty($supplier->email))
                            <span class="me-2">Email: <strong>{{ $supplier->email }}</strong></span>
                        @endif
                        <span class="me-2">Status:
            <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
              {{ ucfirst($supplier->status ?? 'inactive') }}
            </span>
          </span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @if(!empty($supplier->attachment_path))
                    <a href="{{ route('voyager.suppliers.download', $supplier->id) }}" class="btn btn-outline-primary">
                        Attachment
                    </a>
                @endif
                <a href="{{ route('voyager.suppliers.edit', $supplier->id) }}" class="btn btn-outline-secondary">
                    Edit Supplier
                </a>
                <a href="{{ route('voyager.suppliers.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        {{-- Big Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Materials (Total)</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($materialsTotal ?? 0, 2) }} AED
                        </div>
                        @if(!empty($materialsSummary['count']))
                            <div class="small text-muted">Records: {{ $materialsSummary['count'] }}</div>
                        @endif
                        @if(!empty($materialsSummary['latest']))
                            <div class="small text-muted">
                                Latest: {{ \Illuminate\Support\Carbon::parse($materialsSummary['latest'])->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Supplier Payments (Total)</div>
                        <div class="display-6 fw-bold mb-0" style="font-size:1.35rem;">
                            {{ number_format($paymentsTotal ?? 0, 2) }} AED
                        </div>
                        @if(!empty($paymentsSummary['count']))
                            <div class="small text-muted">Transactions: {{ $paymentsSummary['count'] }}</div>
                        @endif
                        @if(!empty($paymentsSummary['latest']))
                            <div class="small text-muted">
                                Latest: {{ \Illuminate\Support\Carbon::parse($paymentsSummary['latest'])->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Remaining</div>
                        @php $remaining = ($materialsTotal ?? 0) - ($paymentsTotal ?? 0); @endphp
                        <div class="display-6 fw-bold mb-0 {{ $remaining > 0 ? 'text-danger' : '' }}"
                             style="font-size:1.35rem;">
                            {{ number_format($remaining, 2) }} AED
                        </div>
                        <div class="small text-muted">Formula: Materials − Payments</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details + Two-Column (Materials / Payments) --}}
        <div class="row g-3 mb-4">
            {{-- Details --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Supplier Details</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="text-muted small">Name</div>
                                <div class="fw-semibold">{{ $supplier->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">City</div>
                                <div class="fw-semibold">{{ $supplier->city->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Status</div>
                                <div>
                <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                  {{ ucfirst($supplier->status ?? 'inactive') }}
                </span>
                                </div>
                            </div>
                            @if(!empty($supplier->address))
                                <div class="col-12">
                                    <div class="text-muted small">Address</div>
                                    <div class="fw-semibold">{{ $supplier->address }}</div>
                                </div>
                            @endif
                            @if(!empty($supplier->email))
                                <div class="col-md-6">
                                    <div class="text-muted small">Email</div>
                                    <div class="fw-semibold">{{ $supplier->email }}</div>
                                </div>
                            @endif
                            @if(!empty($supplier->contact_person_name))
                                <div class="col-md-6">
                                    <div class="text-muted small">Contact</div>
                                    <div class="fw-semibold">{{ $supplier->contact_person_name }}</div>
                                </div>
                            @endif
                            @if(!empty($supplier->contact_person_phone))
                                <div class="col-md-6">
                                    <div class="text-muted small">Phone</div>
                                    <div class="fw-semibold">{{ $supplier->contact_person_phone }}</div>
                                </div>
                            @endif
                            @if(!empty($supplier->note))
                                <div class="col-12">
                                    <div class="text-muted small">Note</div>
                                    <div class="fw-normal">{{ $supplier->note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex flex-wrap gap-2">
                        <a href="{{ route('voyager.supplier_materials.index', ['supplier_id' => $supplier->id]) }}"
                           class="btn btn-sm btn-outline-secondary">All Materials</a>
                        <a href="{{ route('voyager.supplier_payments.index', ['supplier_id' => $supplier->id]) }}"
                           class="btn btn-sm btn-outline-secondary">All Payments</a>
                    </div>
                </div>
            </div>

            {{-- Materials (left column) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Materials</strong>
                        <a href="{{ route('voyager.supplier_materials.create', ['supplier_id' => $supplier->id]) }}"
                           class="btn btn-sm btn-outline-primary">Add</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th class="text-end">Total (AED)</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($materials as $m)
                                    <tr>
                                        <td>{{ optional($m->material_date ?? $m->created_at)->format('Y-m-d') }}</td>
                                        <td>{{ optional($m->project)->name ?? '—' }}</td>
                                        <td class="text-end">{{ number_format(($m->total_price ?? $m->amount ?? 0), 2) }}</td>
                                        <td class="text-truncate" style="max-width:220px;">{{ $m->note ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">No materials.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $materials->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- Payments (right column) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Payments</strong>
                        <a href="{{ route('voyager.supplier_payments.create', ['supplier_id' => $supplier->id]) }}"
                           class="btn btn-sm btn-outline-primary">Add</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Method/Ref</th>
                                    <th class="text-end">Amount (AED)</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($payments as $p)
                                    <tr>
                                        <td>{{ optional($p->paid_at ?? $p->created_at)->format('Y-m-d') }}</td>
                                        <td>{{ $p->method ?? $p->reference ?? '—' }}</td>
                                        <td class="text-end">{{ number_format(($p->amount ?? 0), 2) }}</td>
                                        <td class="text-truncate" style="max-width:220px;">{{ $p->note ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">No payments.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $payments->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
