@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th {
            white-space: nowrap;
        }

        .badge {
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Services</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('voyager.services.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Add Service
                </a>
                <a href="{{ route('voyager.services.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>

        {{-- Stats --}}
        @if(!empty($stats))
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ number_format($stats['total'] ?? 0) }}</div>
                            <div class="text-muted">Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ number_format($stats['published'] ?? 0) }}</div>
                            <div class="text-muted">Published</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ number_format($stats['drafts'] ?? 0) }}</div>
                            <div class="text-muted">Drafts</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ number_format($stats['featured'] ?? 0) }}</div>
                            <div class="text-muted">Featured</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="{{ route('voyager.services.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach(['published' => 'Published', 'draft' => 'Draft', 'archived' => 'Archived'] as $key => $label)
                                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="Title or excerpt">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Order</th>
                            <th>Created At</th>
                            <th>By</th>
                            <th style="width: 160px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($services as $i => $r)
                            <tr>
                                <td>{{ ($services->currentPage() - 1) * $services->perPage() + $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $r->title }}</div>
                                    @if($r->excerpt)
                                        <div class="text-muted small">{{ Str::limit($r->excerpt, 100) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @switch($r->status)
                                        @case('published') <span class="badge bg-success">Published</span> @break
                                        @case('draft')     <span class="badge bg-secondary">Draft</span> @break
                                        @case('archived')  <span class="badge bg-dark">Archived</span> @break
                                        @default           <span
                                                class="badge bg-light text-dark">{{ $r->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($r->is_featured)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                                <td>{{ $r->display_order }}</td>
                                <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $r->creator->name ?? '—' }}</td>
                                <td>
                                    @php $module = 'services'; $u = auth()->user(); @endphp
                                    @if($u->hasPermission("edit_{$module}"))
                                        <a href="{{ route("voyager.$module.edit", $r->id) }}"
                                           class="btn btn-sm btn-warning">تعديل</a>
                                    @endif
                                    @if($u->hasPermission("delete_{$module}"))
                                        <a href="javascript:void(0);"
                                           data-url="{{ route("voyager.$module.destroy", $r->id) }}"
                                           data-id="{{ $r->id }}" class="delete-record btn btn-sm btn-danger">حذف</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No services found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $services->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
