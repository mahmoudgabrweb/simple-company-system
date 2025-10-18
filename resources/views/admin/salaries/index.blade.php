{{-- resources/views/admin/salaries/index.blade.php --}}
@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Salaries</h4>
            <a href="{{ route('voyager.salaries.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Record
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $p)
                            <option value="{{ $p->id }}" @selected(($filters['employee_id'] ?? null) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">All projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(($filters['project_id'] ?? null) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary mt-4" type="submit"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('voyager.salaries.index') }}" class="btn btn-secondary mt-4"><i
                                class="bx bx-reset"></i> Reset</a>
                </div>
            </div>
        </form>

        @if(isset($sumAll))
            <div class="card p-3 mb-3">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="fw-bold">Total Salaries Based on Filters: {{ number_format($sumAll, 2) }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($sumTypes as $t => $total)
                            <span class="badge bg-light text-dark">
                        {{ $paymentsMap[$t] ?? $t }}: {{ number_format($total, 2) }}
                    </span>
                        @endforeach

                        @isset($totalDays)
                            <span class="badge bg-info text-white">
                        Total Days:  {{ number_format($totalDays) }}
                    </span>
                        @endisset
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Project</th>
                        <th>Month</th>
                        <th>Days</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Created</th>
                        <th style="width:120px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($salaries as $i => $salary)
                        <tr>
                            <td>{{ ($salaries->currentPage()-1)*$salaries->perPage() + $i + 1 }}</td>
                            <td>{{ $salary->title }}</td>
                            <td>{{ $salary->project?->name ?? '-' }}</td>
                            <td>{{ $salary->month ? \Carbon\Carbon::parse($salary->month)->format('Y-m') : '-' }}</td>
                            <td>{{ $salary->days_count }}</td>
                            <td>{{ number_format($salary->amount, 2) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($salary->notes, 40) }}</td>
                            <td>{{ $salary->created_at?->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('voyager.salaries.edit', $salary->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.salaries.destroy', $salary->id) }}" method="post"
                                      class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
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
                {{ $salaries->links() }}
            </div>
        </div>
    </div>
@endsection
