{{-- resources/views/admin/variations/show.blade.php --}}
@extends('admin.main')
@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Variation #{{ $variation->id }} — Project: {{ $project->name }}</h4>
            <div>
                <a href="{{ route('voyager.projects.variations.edit',[$project->id,$variation->id]) }}"
                   class="btn btn-primary">Edit</a>
                <a href="{{ route('voyager.projects.variations.index',$project->id) }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card card-body mb-3">
            <div><strong>Status:</strong> {{ ucfirst($variation->status) }}</div>
            <div><strong>Valid until:</strong> {{ $variation->valid_until?->format('Y-m-d') ?? '—' }}</div>
            <div><strong>Totals:</strong>
                Subtotal {{ number_format($variation->subtotal,2) }} {{ $variation->currency }},
                Tax {{ number_format($variation->tax,2) }}, Total {{ number_format($variation->total,2) }}</div>
            <div class="mt-2"><strong>Notes:</strong><br>{{ $variation->notes ?? '—' }}</div>
        </div>

        @foreach($variation->sections as $s)
            <div class="card mb-3">
                <div class="card-header"><strong>{{ $s->title }}</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($s->items as $idx => $it)
                                <tr>
                                    <td>{{ $idx+1 }}</td>
                                    <td>{{ $it->name }}
                                        <div class="text-muted small">{{ $it->description }}</div>
                                    </td>
                                    <td>{{ $it->unit?->name_en ?? '—' }}</td>
                                    <td class="text-end">{{ $it->qty }}</td>
                                    <td class="text-end">{{ number_format($it->unit_price,2) }}</td>
                                    <td class="text-end">
                                        {{ $it->discount_type === 'percent' ? ($it->discount_value.'%') : ($it->discount_type === 'fixed' ? number_format($it->discount_value,2) : '—') }}
                                    </td>
                                    <td class="text-end">{{ number_format($it->subtotal,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
