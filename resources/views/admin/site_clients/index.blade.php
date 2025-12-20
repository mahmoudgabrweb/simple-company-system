@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Site Clients</h4>
            <a class="btn btn-primary" href="{{ route('voyager.site_clients.create') }}">+ New Client</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }}">{{ session('message') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Order</th>
                        <th>Active?</th>
                        <th>Created At</th>
                        <th width="220">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($clients as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->name }}</td>
                            <td>
                                <img style="width: 50px;" src="{{ \Illuminate\Support\Facades\Storage::url($s->image) }}" alt="">
                            </td>
                            <td>{{ $s->display_order }}</td>
                            <td>
                                @if($s->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>{{ $s->created_at?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('voyager.site_clients.edit', $s->id) }}"
                                       class="btn btn-warning">Edit</a>

                                    <form action="{{ route('voyager.site_clients.destroy', $s->id) }}"
                                          method="post"
                                          onsubmit="return confirm('Delete this client?');"
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
                            <td colspan="7" class="text-center text-muted py-4">No clients found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">
            {{ $clients->links() }}
        </div>
    </div>
@endsection
