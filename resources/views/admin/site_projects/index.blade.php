@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Site Projects</h4>
            <a class="btn btn-primary" href="{{ route('voyager.site_projects.create') }}">+ New Project</a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }}">{{ session('message') }}</div>
        @endif

        {{-- Stats (optional) --}}
        @if(isset($stats))
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-muted">Total Projects</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="display-6">{{ $stats['active'] ?? 0 }}</div>
                            <div class="text-muted">Active Projects</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Search --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Search by name, client, category..."
                           value="{{ $q ?? request('q') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Duration</th>
                        <th>Achievements</th>
                        <th>Sliders</th>
                        <th>Active?</th>
                        <th>Created At</th>
                        <th width="220">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($projects as $p)
                        <tr>
                            <td>{{ $p->id }}</td>

                            <td>
                                @if($p->image)
                                    <img src="{{ Storage::url($p->image) }}"
                                         style="height:40px;max-width:80px;border-radius:4px;object-fit:cover;">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $p->name }}</td>
                            <td>{{ $p->client ?: '—' }}</td>
                            <td>{{ $p->category ?: '—' }}</td>
                            <td>{{ $p->duration ?: '—' }}</td>

                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $p->achievements_count ?? $p->achievements->count() ?? 0 }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $p->sliders_count ?? $p->sliders->count() ?? 0 }}
                                </span>
                            </td>

                            <td>
                                @if($p->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>

                            <td>{{ $p->created_at?->format('Y-m-d') ?? '—' }}</td>

                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('voyager.site_projects.edit', $p->id) }}"
                                       class="btn btn-warning">Edit</a>

                                    <form action="{{ route('voyager.site_projects.destroy', $p->id) }}"
                                          method="post"
                                          onsubmit="return confirm('Delete this project?');"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No projects found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-2">
            {{ $projects->links() }}
        </div>
    </div>
@endsection
