@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}">
    <style>
        .select2-container {
            width: 100% !important
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl py-3">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Suppliers</h4>
            <a href="{{ route('voyager.suppliers.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Supplier
            </a>
        </div>

        {{-- Filters (like Projects simple search) --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                           placeholder="Supplier name">
                </div>
                <div class="col-md-5">
                    <label class="form-label">City</label>
                    <select name="city_id" class="form-select js-select2-city" data-placeholder="All cities">
                        <option value="">All</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100"><i class="bx bx-search"></i> Filter</button>
                </div>
            </div>
        </form>

        {{-- Empty state --}}
        @if($suppliers->count() === 0)
            <div class="card p-4 text-center text-muted">No suppliers found.</div>
        @else
            @foreach($suppliers as $s)
                @php
                    $f = $finance[$s->id] ?? [];
                    $materials = (float)($f['materials'] ?? 0);
                    $payments  = (float)($f['payments']  ?? 0);
                    $remaining = (float)($f['remaining'] ?? ($materials - $payments));
                @endphp

                <div class="card p-3 mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="m-0">{{ $s->name }}</h5>
                            <div class="text-muted small">
                                City: {{ $s->city->name ?? '—' }}
                                · Contact: {{ $s->contact_person_name ?? '—' }}
                                · Phone: {{ $s->contact_person_phone ?? '—' }}
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('voyager.suppliers.edit', $s->id) }}" class="btn btn-warning">Edit</a>
                            @if($s->attachment_path)
                                <a href="{{ route('voyager.suppliers.download', $s->id) }}"
                                   class="btn btn-outline-primary">Attachment</a>
                            @endif
                            <a href="{{ route('voyager.suppliers.show', $s->id) }}" class="btn btn-outline-secondary">
                                Show
                            </a>
                        </div>

                    </div>

                    {{-- Stats row: Payments / Materials / Remaining --}}
                    <div class="row g-2 mt-2">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Materials</div>
                                <div class="fs-5 fw-bold">{{ number_format($materials, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Total Supplier Payments</div>
                                <div class="fs-5 fw-bold">{{ number_format($payments, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Remaining</div>
                                <div class="fs-5 fw-bold {{ $remaining > 0 ? 'text-danger' : '' }}">
                                    {{ number_format($remaining, 2) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted small">Status</div>
                                <div>
                  <span class="badge bg-{{ $s->status === 'active' ? 'success' : 'secondary' }}">
                    {{ ucfirst($s->status) }}
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted mt-1">
                        * Remaining = Materials − Total Supplier Payments.
                    </div>
                </div>
            @endforeach

            <div class="mt-2">{{ $suppliers->links() }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('.js-select2-city').select2({theme: 'bootstrap-5', placeholder: 'All cities', allowClear: true});
        });
    </script>
@endpush
