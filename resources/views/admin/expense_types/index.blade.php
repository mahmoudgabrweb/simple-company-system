@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th { white-space: nowrap; }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Expense Types</h4>
            <a href="{{ route('voyager.expense_types.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Type
            </a>
        </div>

        {{-- Stats --}}
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['total'] ?? ($expenseTypes->total() ?? 0) }}</div>--}}
{{--                        <div class="text-muted">Total</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- Table (Laravel pagination) --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th class="text-end" style="width:140px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($expenseTypes as $t)
                            <tr>
                                <td>{{ ($expenseTypes->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $t->name }}</td>
                                <td>{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('voyager.expense_types.edit', $t->id) }}"
                                       class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('voyager.expense_types.destroy', $t->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this type?');">
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
                                <td colspan="4" class="text-center text-muted">No data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $expenseTypes->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
