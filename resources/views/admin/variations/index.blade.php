@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Variations — Project: {{ $project->name }}</h4>
            <a href="{{ route('voyager.projects.variations.create',$project->id) }}" class="btn btn-primary">Add
                Variation</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Valid Until</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($variations as $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td>{{ $v->title ?? '—' }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($v->status) }}</span></td>
                            <td>{{ number_format($v->total,2) }} {{ $v->currency }}</td>
                            <td>{{ $v->valid_until?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $v->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('voyager.projects.variations.show',[$project->id,$v->id]) }}">View</a>
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('voyager.projects.variations.edit',[$project->id,$v->id]) }}">Edit</a>
                                <form action="{{ route('voyager.projects.variations.destroy',[$project->id,$v->id]) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete variation #{{ $v->id }}?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">No variations yet</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $variations->links() }}
            </div>
        </div>
    </div>
@endsection
