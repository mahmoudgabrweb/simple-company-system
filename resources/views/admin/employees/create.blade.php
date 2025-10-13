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

        .thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
            border-radius: .375rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Employee</h4>
            <a href="{{ route('voyager.employees.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('voyager.employees.store') }}" method="post" enctype="multipart/form-data">
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
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Job <span class="text-danger">*</span></label>
                        <select name="job_id" class="form-select" required>
                            <option value="">— Choose —</option>
                            @foreach($jobs as $j)
                                <option value="{{ $j->id }}" @selected(old('job_id') == $j->id)>{{ $j->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_at" class="form-control" value="{{ old('start_at') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_at" class="form-control" value="{{ old('end_at') }}">
                    </div>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Salary ({{ config('app.currency', 'SAR') }})</label>
                        <input type="number" step="0.01" min="0" name="salary" class="form-control"
                               value="{{ old('salary', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">CV (PDF/DOC)</label>
                        <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Photo</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control"
                               value="{{ $currentCompany->name ?? 'Will be saved to the current company' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('voyager.employees.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        if ($.fn.select2) {
            $.fn.select2.defaults.set('theme', 'bootstrap-5');
            $.fn.select2.defaults.set('width', '100%');
        }
        $(function () {
            $('select.form-select').select2({minimumResultsForSearch: 10});
        });
    </script>
@endsection
