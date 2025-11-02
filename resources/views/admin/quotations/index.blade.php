@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Quotations</h4>
            <a class="btn btn-primary" href="{{ route('voyager.quotations.create') }}">+ New Quotation</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Optional filters (render only if provided by controller) --}}
        @if(isset($projects) || isset($statuses))
            <form method="get" class="card p-3 mb-3">
                <div class="row g-2 align-items-end">
                    @isset($projects)
                        <div class="col-md-4">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">— All —</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endisset

                    @isset($statuses)
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">— All —</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endisset

                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-secondary w-100">Filter</button>
                    </div>
                </div>
            </form>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Number</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Active?</th>
                        <th>Total</th>
                        <th width="280">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($quotations as $q)
                        <tr>
                            <td>{{ $q->id }}</td>
                            <td>{{ $q->project->name ?? '—' }}</td>
                            <td>{{ $q->quotation_number ?? '—' }}</td>
                            <td>{{ $q->version }}</td>
                            <td><span class="badge bg-secondary">{{ $q->status }}</span></td>
                            <td>
                                @if($q->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>{{ number_format($q->total_amount, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('voyager.quotations.edit', $q->id) }}" class="btn btn-warning">Edit</a>

                                    <a href="{{ route('voyager.quotations.pdf', $q->id) }}" class="btn btn-warning">PDF</a>

                                    <form action="{{ route('voyager.quotations.destroy', $q->id) }}" method="post"
                                          onsubmit="return confirm('Delete this quotation?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No quotations found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">{{ $quotations->links() }}</div>
    </div>
@endsection
