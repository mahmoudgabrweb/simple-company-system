@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th { white-space: nowrap; }
        .label {
            display: inline-block;
            padding: .35rem .6rem;
            font-size: .75rem;
            border-radius: .25rem;
        }
        .label-success { background: #d1e7dd; color: #0f5132; }
        .label-danger  { background: #f8d7da; color: #842029; }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Employees</h4>
            <a href="{{ route('voyager.employees.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Employee
            </a>
        </div>

        {{-- Stats --}}
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['total'] ?? ($employees->total() ?? 0) }}</div>--}}
{{--                        <div class="text-muted">Total Employees</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['active'] ?? 0 }}</div>--}}
{{--                        <div class="text-success">Active</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['inactive'] ?? 0 }}</div>--}}
{{--                        <div class="text-danger">Inactive</div>--}}
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
                            <th>Job</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Salary</th>
                            <th class="text-end" style="width: 180px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>{{ ($employees->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $emp->name ?? '—' }}</td>
                                <td>
                                    @php
                                        // Support relation job->name OR a plain column job_name
                                        $jobName = null;
                                        if (isset($emp->job) && is_object($emp->job)) $jobName = $emp->job->name ?? null;
                                        if (!$jobName && array_key_exists('job_name', $emp->getAttributes())) $jobName = $emp->job_name;
                                    @endphp
                                    {{ $jobName ?? '—' }}
                                </td>
                                <td>{{ $emp->phone ?? '—' }}</td>
                                <td>{{ $emp->email ?? '—' }}</td>
                                <td>
                                    @if (array_key_exists('is_active', $emp->getAttributes()))
                                        @if ($emp->is_active)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Inactive</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ optional($emp->start_at)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ optional($emp->end_at)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ isset($emp->salary) ? number_format((float)$emp->salary, 2) : '—' }}</td>
                                <td class="text-end">
                                    @if (Route::has('voyager.employees.toggle') && array_key_exists('is_active', $emp->getAttributes()))
                                        <form action="{{ route('voyager.employees.toggle', $emp->id) }}"
                                              method="POST" class="d-inline-block me-1">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm {{ $emp->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                {{ $emp->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('voyager.employees.edit', $emp->id) }}"
                                       class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('voyager.employees.destroy', $emp->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this employee?');">
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
                    {{ $employees->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
