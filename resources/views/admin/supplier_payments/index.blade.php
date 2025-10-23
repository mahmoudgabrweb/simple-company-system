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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Supplier Payments</h4>
            <a href="{{ route('voyager.supplier_payments.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Payment
            </a>
        </div>

        {{-- Filters --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select js-select2-supplier">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Payment Type</label>
                    <select name="payment_type" class="form-select">
                        <option value="">All types</option>
                        @foreach($types as $k=>$v)
                            <option value="{{ $k }}" @selected(request('payment_type')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1 d-flex gap-2">
                    <button class="btn btn-primary mt-4 w-100"><i class="bx bx-search"></i></button>
                </div>
            </div>
        </form>

        {{-- Totals (filtered) --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <div class="display-6 text-success">{{ number_format($sumAll, 2) }}</div>
                        <div class="text-muted">Total Amount (filtered)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Supplier</th>
                        <th>Material</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Paid By</th>
                        <th>Paid At</th>
                        <th>Attachment</th>
                        <th style="width:130px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $i => $p)
                        <tr>
                            <td>{{ ($payments->currentPage()-1)*$payments->perPage() + $i + 1 }}</td>
                            <td>{{ $p->title }}</td>
                            <td>{{ $p->supplier?->name ?? '-' }}</td>
                            <td>{{ $p->material?->title ?? '—' }}</td>
                            <td>{{ number_format($p->amount,2) }}</td>
                            <td>{{ ucfirst($p->payment_type) }}</td>
                            <td>{{ $p->employee?->name ?? '—' }}</td>
                            <td>{{ $p->paid_at?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                @if($p->attachment_path)
                                    <a href="{{ route('voyager.supplier_payments.download',$p->id) }}"
                                       class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('voyager.supplier_payments.edit',$p->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.supplier_payments.destroy',$p->id) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No payments found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('.js-select2-supplier').select2({theme: 'bootstrap-5', placeholder: 'All suppliers', allowClear: true});
        });
    </script>
@endpush
