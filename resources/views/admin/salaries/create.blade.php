@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}"/>
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Salary Record</h4>
            <a href="{{ route('voyager.salaries.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('voyager.salaries.store') }}" method="post">
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
                    <div class="col-md-4">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" @selected(old('employee_id')==$e->id)>{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Month <span class="text-danger">*</span></label>
                        <input type="month" name="month" class="form-control" value="{{ old('month') }}" required>
                    </div>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach(['salary'=>'Salary','overtime'=>'Overtime','bonus'=>'Bonus'] as $k=>$v)
                                <option value="{{ $k }}" @selected(old('type','salary')==$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Accounting Type</label>
                        <select name="expense_type_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" @selected(old('expense_type_id')==$t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control"
                               value="{{ old('amount', 0) }}" required>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($projects as $t)
                                <option value="{{ $t->id }}" @selected(old('project_id')==$t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Days Count</label>
                        <input type="number" min="0" max="365" name="days_count" class="form-control"
                               value="{{ old('days_count', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control"
                               value="{{ $currentCompany->name ?? 'Will be saved under the current company' }}"
                               disabled>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('voyager.salaries.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        if ($.fn.select2) {
            $.fn.select2.defaults.set('theme', 'bootstrap-5');
            $.fn.select2.defaults.set('dir', 'ltr');
            $.fn.select2.defaults.set('width', '100%');
        }
        $(function () {
            $('select.form-select').select2({minimumResultsForSearch: 10});
        });
    </script>
@endsection
