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
            <h4 class="m-0">Edit Supplier Material #{{ $material->id }}</h4>
            <a href="{{ route('voyager.supplier_materials.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.supplier_materials.update', $material->id) }}" method="POST"
              enctype="multipart/form-data" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select js-select2-supplier" required>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id',$material->supplier_id)==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Project *</label>
                    <select name="project_id" class="form-select js-select2-project" required>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('project_id',$material->project_id)==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title',$material->title) }}"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" name="amount" class="form-control"
                           value="{{ old('amount',$material->amount) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Paid By Employee</label>
                    <select name="paid_by_employee_id" class="form-select js-select2-employee">
                        <option value="">Select employee</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('paid_by_employee_id',$material->paid_by_employee_id)==$e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Paid At</label>
                    <input type="datetime-local" name="paid_at" class="form-control"
                           value="{{ old('paid_at', $material->paid_at?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3">{{ old('description',$material->description) }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Invoice Attachment</label>
                    <input type="file" name="invoice_attachment" class="form-control">
                    @if($material->invoice_attachment_path)
                        <div class="mt-1">
                            <a href="{{ route('voyager.supplier_materials.download',$material->id) }}"
                               class="small text-primary">Current file</a>
                        </div>
                    @endif
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
            $('.js-select2-supplier, .js-select2-project, .js-select2-employee').select2({
                theme: 'bootstrap-5',
                allowClear: true
            });
        });
    </script>
@endpush
