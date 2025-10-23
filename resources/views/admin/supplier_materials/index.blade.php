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
            <h4 class="m-0">Supplier Materials</h4>
            <a href="{{ route('voyager.supplier_materials.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Record
            </a>
        </div>

        {{-- Filters --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select js-select2-supplier">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select js-select2-project">
                        <option value="">All projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
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
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary mt-4 w-100"><i class="bx bx-search"></i> Filter</button>
                </div>
            </div>
        </form>

        {{-- Summary Totals --}}
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
                        <th>Project</th>
                        <th>Amount</th>
                        <th>Paid By</th>
                        <th>Paid At</th>
                        <th>Attachment</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($materials as $i => $m)
                        <tr>
                            <td>{{ ($materials->currentPage()-1)*$materials->perPage() + $i + 1 }}</td>
                            <td>{{ $m->title }}</td>
                            <td>{{ $m->supplier?->name ?? '-' }}</td>
                            <td>{{ $m->project?->name ?? '-' }}</td>
                            <td>{{ number_format($m->amount,2) }}</td>
                            <td>{{ $m->employee?->name ?? '-' }}</td>
                            <td>{{ $m->paid_at?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                @if($m->invoice_attachment_path)
                                    <a href="{{ route('voyager.supplier_materials.download',$m->id) }}"
                                       class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('voyager.supplier_materials.edit',$m->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.supplier_materials.destroy',$m->id) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('.js-select2-supplier, .js-select2-project').select2({
                theme: 'bootstrap-5', placeholder: 'Select...', allowClear: true
            });
        });
    </script>
@endpush
