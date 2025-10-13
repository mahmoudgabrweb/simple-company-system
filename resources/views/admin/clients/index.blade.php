@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Clients</h4>
            <a href="{{ route('voyager.clients.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Client
            </a>
        </div>

        {{-- Stats --}}
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-md-4">--}}
{{--                <div class="card h-100">--}}
{{--                    <div class="card-body text-center">--}}
{{--                        <div class="display-6">{{ $stats['total'] ?? ($clients->total() ?? 0) }}</div>--}}
{{--                        <div class="text-muted">Total Clients</div>--}}
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
                            <th>Phone</th>
                            <th>Alt. Phone</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Created At</th>
                            <th class="text-end" style="width:160px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ ($clients->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $client->name ?? '—' }}</td>
                                <td>{{ $client->phone ?? '—' }}</td>
                                <td>{{ $client->alternative_phone ?? '—' }}</td>
                                <td>{{ $client->email ?? '—' }}</td>
                                <td>
                                    @php
                                        // Support either relation `city` or stored string column
                                        $cityName = null;
                                        if (isset($client->city)) {
                                            $cityName = $client->city->name ?? null;
                                        } elseif (array_key_exists('city', $client->getAttributes())) {
                                            $cityName = $client->city;
                                        } elseif (array_key_exists('city_name', $client->getAttributes())) {
                                            $cityName = $client->city_name;
                                        }
                                    @endphp
                                    {{ $cityName ?? '—' }}
                                </td>
                                <td class="text-truncate" style="max-width:280px">
                                    {{ $client->address ?? '—' }}
                                </td>
                                <td>{{ optional($client->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('voyager.clients.edit', $client->id) }}"
                                       class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('voyager.clients.destroy', $client->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this client?');">
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
                                <td colspan="9" class="text-center text-muted">No data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $clients->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    {{-- No DataTables scripts needed --}}
@endsection
