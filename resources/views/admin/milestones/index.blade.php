@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}">
    <style>
        .select2-container {
            width: 100% !important
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            display: flex;
            align-items: center;
            background: #fff
        }

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Milestones</h4>
            <a href="{{ route('voyager.milestones.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add
                Milestone</a>
        </div>

        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" id="filter_type" class="form-select">
                        <option value="">All</option>
                        <option value="quotation" @selected(request('type')==='quotation')>Quotation</option>
                        <option value="variation" @selected(request('type')==='variation')>Variation</option>
                    </select>
                </div>
                <div class="col-md-4" id="filter_parent_wrap">
                    <label class="form-label">Parent</label>
                    <select name="parent_id" id="filter_parent" class="form-select js-select2-parent">
                        <option value="">All</option>
                        @if(request('type')==='variation')
                            @foreach($variations as $v)
                                <option value="{{ $v->id }}"
                                        @selected(request('parent_id')==$v->id) data-type="variation">{{ $v->title ?? ('Variation #'.$v->id) }}</option>
                            @endforeach
                        @else
                            @foreach($quotations as $q)
                                <option value="{{ $q->id }}"
                                        @selected(request('parent_id')==$q->id) data-type="quotation">{{ $q->title ?? ('Quotation #'.$q->id) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statuses as $k=>$v)
                            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Due from</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Due to</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="mt-2 text-end">
                <button class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
            </div>
        </form>

        {{-- Totals --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <div class="display-6 text-success">{{ number_format($sumAll, 2) }}</div>
                        <div class="text-muted">Total Amount (filtered)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Parent</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Attachment</th>
                        <th style="width:130px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($milestones as $i => $m)
                        <tr>
                            <td>{{ ($milestones->currentPage()-1)*$milestones->perPage() + $i + 1 }}</td>
                            <td>{{ $m->title }}</td>
                            <td>
                                @php
                                    $t = class_basename($m->milestonable_type);
                                @endphp
                                {{ $t }}
                            </td>
                            <td>
                                @if($m->milestonable)
                                    {{ $m->milestonable->title ?? ('#'.$m->milestonable->id) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ number_format($m->amount,2) }}</td>
                            <td>
                                <span class="badge bg-{{ $m->status==='paid' ? 'success' : ($m->status==='cancelled' ? 'secondary' : 'info') }}">{{ ucfirst($m->status) }}</span>
                            </td>
                            <td>{{ $m->due_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                @if($m->attachment_path)
                                    <a href="{{ route('voyager.milestones.download',$m->id) }}"
                                       class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('voyager.milestones.edit',$m->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.milestones.destroy',$m->id) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this milestone?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No milestones found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $milestones->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            const $type = $('#filter_type');
            const $parent = $('#filter_parent');

            $('.js-select2-parent').select2({theme: 'bootstrap-5', allowClear: true});

            function reloadParentOptions() {
                const type = $type.val();
                let opts = '<option value="">All</option>';

                if (type === 'variation') {
                    @foreach($variations as $v)
                        opts += `<option value="{{ $v->id }}" data-type="variation">{{ $v->title ?? ('Variation #'.$v->id) }}</option>`;
                    @endforeach
                } else if (type === 'quotation') {
                    @foreach($quotations as $q)
                        opts += `<option value="{{ $q->id }}" data-type="quotation">{{ $q->title ?? ('Quotation #'.$q->id) }}</option>`;
                    @endforeach
                } else {
                    // default show quotations (or empty); keeping empty simplifies UX
                }
                $parent.html(opts).val('{{ request('parent_id') }}').trigger('change.select2');
            }

            $type.on('change', reloadParentOptions);
        });
    </script>
@endpush
