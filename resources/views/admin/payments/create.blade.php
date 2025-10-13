@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Payment</h4>
            <a href="{{ route('voyager.payments.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('voyager.payments.store') }}" method="post" enctype="multipart/form-data"
              class="card p-3">
            @csrf
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">— Choose Project —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                           value="{{ old('amount') }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Method</label>
                    <select name="payment_method" class="form-select" required>
                        @foreach($methods as $k => $v)
                            <option value="{{ $k }}" @selected(old('payment_method')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="datetime-local" name="paid_at" class="form-control" value="{{ old('paid_at') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Paid By</label>
                    <input name="paid_by" class="form-control" value="{{ old('paid_by') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Received By (Employee)</label>
                    <select name="received_by_employee_id" class="form-select">
                        <option value="">—</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('received_by_employee_id')==$e->id)>
                                {{ $e->name }} — {{ $e->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Reference</label>
                    <input name="reference" class="form-control" value="{{ old('reference') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Attachment (Receipt/Image/PDF)</label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.webp">
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('voyager.payments.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
