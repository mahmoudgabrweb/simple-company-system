@extends('admin.main')

@section('css_sheets')
    <style>.table thead th {
            white-space: nowrap;
        }</style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Salaries</h4>
            <a href="{{ route('voyager.salaries.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Record
            </a>
        </div>

        {{-- Stats --}}
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['total'] ?? ($salaries->total() ?? 0) }}</div>--}}
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

        {{-- Table (Laravel pagination) --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Type</th>
                            <th>Accounting Type</th>
                            <th>Amount</th>
                            <th>Days</th>
                            <th>Created At</th>
                            <th class="text-end" style="width:160px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($salaries as $sal)
                            <tr>
                                <td>{{ ($salaries->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $sal->title }}</td>
                                <td>
                                    @php
                                        $empName = isset($sal->employee) ? ($sal->employee->name ?? null) : null;
                                        if (!$empName && array_key_exists('employee_name', $sal->getAttributes())) {
                                            $empName = $sal->employee_name;
                                        }
                                    @endphp
                                    {{ $empName ?? '—' }}
                                </td>
                                <td>
                                    @php
                                        // supports Carbon date or stored 'YYYY-MM' string
                                        $monthVal = $sal->month ?? null;
                                        $monthStr = is_object($monthVal) && method_exists($monthVal,'format')
                                            ? $monthVal->format('Y-m')
                                            : (is_string($monthVal) ? $monthVal : '—');
                                    @endphp
                                    {{ $monthStr }}
                                </td>
                                <td>
                                    @php
                                        $typeMap = ['salary'=>'Salary','overtime'=>'Overtime','bonus'=>'Bonus'];
                                        $typeLabel = $typeMap[$sal->type ?? ''] ?? ($sal->type ?? '—');
                                    @endphp
                                    {{ $typeLabel }}
                                </td>
                                <td>
                                    @php
                                        $etName = isset($sal->expenseType) ? ($sal->expenseType->name ?? null) : null;
                                        if (!$etName && array_key_exists('expense_type_name', $sal->getAttributes())) {
                                            $etName = $sal->expense_type_name;
                                        }
                                    @endphp
                                    {{ $etName ?? '—' }}
                                </td>
                                <td>{{ number_format((float)$sal->amount, 2) }}</td>
                                <td>{{ $sal->days_count ?? '—' }}</td>
                                <td>{{ optional($sal->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('voyager.salaries.edit', $sal->id) }}"
                                       class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('voyager.salaries.destroy', $sal->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this record?');">
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
                                <td colspan="10" class="text-center text-muted">No data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $salaries->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
