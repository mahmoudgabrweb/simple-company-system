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
            <h4 class="m-0">Add Project</h4>
            <a href="{{ route('voyager.projects.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('voyager.projects.store') }}" method="post">
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
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            <option value="">— Choose —</option>
                            @foreach($clients as $cl)
                                <option value="{{ $cl->id }}" @selected(old('client_id')==$cl->id)>{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select" required>
                            <option value="">— Choose —</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}" @selected(old('city_id')==$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="3" class="form-control">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Location (short desc / coordinates)</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Google Map (URL/Embed)</label>
                        <textarea name="map" rows="3" class="form-control"
                                  placeholder="Google Maps URL or embed code">{{ old('map') }}</textarea>
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
                <a href="{{ route('voyager.projects.index') }}" class="btn btn-secondary">Back</a>
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
