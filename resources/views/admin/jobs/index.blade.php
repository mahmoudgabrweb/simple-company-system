@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Jobs</h4>
            <a class="btn btn-primary" href="{{ route('voyager.jobs.create') }}">+ New Job</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Optional filters (render only if provided by controller) --}}
        @if(isset($statuses))
            <form method="get" class="card p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control"
                               value="{{ request('q') }}" placeholder="Title or description">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">— All —</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

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
                        <th>Title</th>
                        <th>Description</th>
                        <th>Active?</th>
                        <th>Created At</th>
                        <th width="280">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td class="fw-semibold">{{ $job->title }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($job->description, 120) }}</td>
                            <td>
                                @if($job->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>{{ optional($job->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('voyager.jobs.edit', $job->id) }}"
                                       class="btn btn-warning">Edit</a>

                                    <form action="{{ route('voyager.jobs.toggle', $job->id) }}" method="post"
                                          onsubmit="return confirm('Toggle this job status?');">
                                        @csrf
                                        <button class="btn btn-outline-secondary" type="submit">Toggle</button>
                                    </form>

                                    <form action="{{ route('voyager.jobs.destroy', $job->id) }}" method="post"
                                          onsubmit="return confirm('Delete this job?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No jobs found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">{{ $jobs->links() }}</div>
    </div>
@endsection
