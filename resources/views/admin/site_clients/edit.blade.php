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

        .icon-preview {
            max-height: 80px;
            border-radius: 6px;
            margin-top: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Client: {{ $client->title }}</h4>
            <a href="{{ route('voyager.site_clients.index') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Form --}}
        <form action="{{ route('voyager.site_clients.update', $client->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
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

                {{-- Row 1 --}}
                <div class="row g-3">

                    {{-- Title --}}
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $client->name) }}"
                               required>
                    </div>

                    {{-- Icon Upload --}}
                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <img style="width: 50px;" src="{{ \Illuminate\Support\Facades\Storage::url($client->image) }}" alt="">
                        <input type="file"
                               name="image"
                               class="form-control">
                    </div>

                    {{-- Display Order --}}
                    <div class="col-md-4">
                        <label class="form-label">Display Order</label>
                        <input type="number"
                               name="display_order"
                               class="form-control"
                               value="{{ old('display_order', $client->display_order) }}"
                               min="0">
                    </div>
                </div>

                {{-- Row 2 --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                    {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active?
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Buttons --}}
            <div class="mt-3">
                <button class="btn btn-primary">Update</button>
                <a href="{{ route('voyager.site_clients.index') }}" class="btn btn-secondary">Back</a>
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
