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
            <h4 class="m-0">Edit Supplier Payment #{{ $payment->id }}</h4>
            <a href="{{ route('voyager.supplier_payments.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.supplier_payments.update',$payment->id) }}" method="POST"
              enctype="multipart/form-data" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select js-select2-supplier" required>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id',$payment->supplier_id)==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier Material (optional)</label>
                    <select name="supplier_material_id" class="form-select js-select2-material">
                        <option value="">Not linked</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}" data-supplier="{{ $m->supplier_id }}"
                                    @selected(old('supplier_material_id',$payment->supplier_material_id)==$m->id)>
                                {{ $m->title }} — {{ $m->supplier?->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Tip: choosing a supplier will filter this list.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title',$payment->title) }}"
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" name="amount" class="form-control"
                           value="{{ old('amount',$payment->amount) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Payment Type *</label>
                    <select name="payment_type" class="form-select" required>
                        @foreach($types as $k=>$v)
                            <option value="{{ $k }}" @selected(old('payment_type',$payment->payment_type)==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Paid By Employee</label>
                    <select name="paid_by_employee_id" class="form-select js-select2-employee">
                        <option value="">Select employee</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('paid_by_employee_id',$payment->paid_by_employee_id)==$e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Paid At</label>
                    <input type="datetime-local" name="paid_at" class="form-control"
                           value="{{ old('paid_at', $payment->paid_at?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3">{{ old('description',$payment->description) }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                    @if($payment->attachment_path)
                        <div class="mt-1">
                            <a class="small text-primary"
                               href="{{ route('voyager.supplier_payments.download',$payment->id) }}">Current file</a>
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
            const $supplier = $('.js-select2-supplier');
            const $material = $('.js-select2-material');
            $('.js-select2-supplier,.js-select2-material,.js-select2-employee').select2({
                theme: 'bootstrap-5',
                allowClear: true
            });

            function filterMaterialsBySupplier() {
                const supplierId = $supplier.val();
                $material.find('option').each(function () {
                    const s = $(this).data('supplier');
                    if (!s || !supplierId) {
                        $(this).show();
                        return;
                    }
                    $(this).toggle(String(s) === String(supplierId));
                });
                // Reset material if mismatched
                const selected = $material.find('option:selected');
                if (selected.length && selected.data('supplier') && String(selected.data('supplier')) !== String(supplierId)) {
                    $material.val('').trigger('change');
                }
            }

            $supplier.on('change', filterMaterialsBySupplier);
            filterMaterialsBySupplier();
        });
    </script>
@endpush
