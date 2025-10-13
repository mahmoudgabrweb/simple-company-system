@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Expense Type</h4>
            <a href="{{ route('voyager.expense_types.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('voyager.expense_types.store') }}" method="post">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control"
                               value="{{ $currentCompany->name ?? 'Will be saved to the current company' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('voyager.expense_types.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
