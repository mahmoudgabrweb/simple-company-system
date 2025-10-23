@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}">
    <style>.select2-container {
            width: 100% !important
        }</style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Milestone #{{ $milestone->id }}</h4>
            <a href="{{ route('voyager.milestones.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.milestones.update',$milestone->id) }}" method="POST"
              enctype="multipart/form-data" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Type *</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="quotation" @selected($currentType==='quotation')>Quotation</option>
                        <option value="variation" @selected($currentType==='variation')>Variation</option>
                    </select>
                </div>

                <div class="col-md-9">
                    <label class="form-label">Parent *</label>
                    <select name="parent_id" id="parent" class="form-select js-select2-parent" required>
                        <option value="">Select...</option>
                        @foreach($quotations as $q)
                            <option value="{{ $q->id }}"
                                    data-type="quotation" @selected($currentType==='quotation' && $currentParentId==$q->id)>{{ $q->title ?? ('Quotation #'.$q->id) }}</option>
                        @endforeach
                        @foreach($variations as $v)
                            <option value="{{ $v->id }}"
                                    data-type="variation" @selected($currentType==='variation' && $currentParentId==$v->id)>{{ $v->title ?? ('Variation #'.$v->id) }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">List filters by selected type.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title',$milestone->title) }}"
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" name="amount" class="form-control"
                           value="{{ old('amount',$milestone->amount) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control"
                           value="{{ old('due_date', $milestone->due_date?->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach($statuses as $k=>$v)
                            <option value="{{ $k }}" @selected(old('status',$milestone->status)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order_index" class="form-control" min="1"
                           value="{{ old('order_index',$milestone->order_index) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                    @if($milestone->attachment_path)
                        <div class="mt-1">
                            <a class="small text-primary"
                               href="{{ route('voyager.milestones.download',$milestone->id) }}">Current file</a>
                        </div>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3">{{ old('description',$milestone->description) }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            const $type = $('#type');
            const $parent = $('#parent');

            $('.js-select2-parent').select2({theme: 'bootstrap-5', allowClear: true});

            function filterParentByType() {
                const t = $type.val();
                $parent.find('option').each(function () {
                    const dt = $(this).data('type');
                    if (!dt) return;
                    $(this).toggle(String(dt) === String(t));
                });
                const sel = $parent.find('option:selected');
                if (sel.length && sel.data('type') && String(sel.data('type')) !== String(t)) {
                    $parent.val('').trigger('change');
                }
            }

            $type.on('change', filterParentByType);
            filterParentByType();
        });
    </script>
@endpush
