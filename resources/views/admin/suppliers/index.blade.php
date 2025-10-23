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
            <h4 class="m-0">Suppliers</h4>
            <a href="{{ route('voyager.suppliers.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Supplier
            </a>
        </div>

        {{-- Filters --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                           placeholder="Search by name">
                </div>

                <div class="col-md-5">
                    <label class="form-label">City</label>
                    <select name="city_id" class="form-select js-select2-city" data-placeholder="All cities">
                        <option value="">All</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Attachment</th>
                        <th>Created</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($suppliers as $i => $s)
                        <tr>
                            <td>{{ ($suppliers->currentPage()-1)*$suppliers->perPage() + $i + 1 }}</td>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->city?->name ?? '-' }}</td>
                            <td>{{ $s->contact_person_name }}</td>
                            <td>{{ $s->contact_person_phone }}</td>
                            <td>
                                <span class="badge bg-{{ $s->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td>
                                @if($s->attachment_path)
                                    <a href="{{ route('voyager.suppliers.download', $s->id) }}"
                                       class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $s->created_at?->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('voyager.suppliers.edit', $s->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.suppliers.destroy', $s->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this supplier?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No suppliers found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                {{ $suppliers->links() }}
            </div>
        </div>
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
