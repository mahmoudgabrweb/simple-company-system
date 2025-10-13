@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Expenses</h4>
            <a href="{{ route('voyager.expenses.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Expense
            </a>
        </div>

        {{-- Stats --}}
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['total'] ?? ($expenses->total() ?? 0) }}</div>--}}
{{--                        <div class="text-muted">Records</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ number_format($stats['amount'] ?? 0, 2) }}</div>--}}
{{--                        <div class="text-muted">Total Amount</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- Filters (GET) --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('voyager.expenses.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Expense Date</label>
                        <input type="date" name="spent_at" class="form-control" value="{{ request('spent_at') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a href="{{ route('voyager.expenses.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table (Laravel pagination) --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Expense Date</th>
                            <th>Created At</th>
                            <th class="text-end" style="width:160px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td>{{ ($expenses->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $exp->title }}</td>
                                <td>
                                    @php
                                        // Works with relation "type" or a plain column
                                        $typeName = isset($exp->type) && is_object($exp->type)
                                            ? ($exp->type->name ?? null)
                                            : ($exp->type_name ?? ($exp->type ?? null));
                                    @endphp
                                    {{ $typeName ?? '—' }}
                                </td>
                                <td>{{ number_format((float)$exp->amount, 2) }}</td>
                                <td>
                                    @php
                                        $byName = isset($exp->creator) ? ($exp->creator->name ?? null) : null;
                                        if (!$byName && array_key_exists('by', $exp->getAttributes())) $byName = $exp->by;
                                    @endphp
                                    {{ $byName ?? '—' }}
                                </td>
                                <td>{{ optional($exp->spent_at)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ optional($exp->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('voyager.expenses.edit', $exp->id) }}"
                                       class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('voyager.expenses.destroy', $exp->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $expenses->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
