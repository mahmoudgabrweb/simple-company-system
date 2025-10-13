@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        <span>Roles</span>
                        <a href="{{ url('/admin/roles/create-new') }}" class="btn btn-success">Add Role</a>
                    </h5>

                    <div class="card-body">
                        {{-- Flash --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Filters --}}
                        {{-- <form method="GET" action="{{ route('voyager.roles.index-new') }}" class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Name / Display Name</label>
                                <input type="text" name="q" class="form-control" placeholder="Search..."
                                       value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <small class="text-muted">Applies only if roles table has an is_active field</small>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="{{ route('voyager.roles.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form> --}}

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name (system)</th>
                                    <th>Display Name</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->display_name ?? '—' }}</td>
                                        <td>
                                            @if (array_key_exists('is_active', $role->getAttributes()))
                                                @if ($role->is_active)
                                                    <span class="badge bg-label-success">Active</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Inactive</span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($role->created_at)->format('Y-m-d H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('voyager.roles.edit', $role->id) }}"
                                               class="btn btn-sm btn-primary">Edit</a>
                                            @if ($role->name !== 'admin')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-url="{{ route('voyager.roles.destroy', $role->id) }}">
                                                    Delete
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No data</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $roles->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_scripts')
    <script>
        // Delete (uses native confirm; swap to a modal if preferred)
        document.querySelectorAll('.btn-delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('Are you sure you want to delete this role?')) return;
                const url = this.dataset.url;
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(r => r.ok ? location.reload() : r.json().then(j => Promise.reject(j)))
                    .catch(() => alert('Failed to delete the role'));
            });
        });
    </script>
@stop
