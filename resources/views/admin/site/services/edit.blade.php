@extends('admin.main')

@section('css_sheets')
    <style>
        .form-card .card-header {
            background: #fff;
            border-bottom: 0;
        }

        .form-card .required:after {
            content: " *";
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Service #{{ $service->id }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('voyager.services.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('voyager.services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card form-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Basic Info</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Title</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $service->title) }}" maxlength="190" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['published'=>'Published','draft'=>'Draft','archived'=>'Archived'] as $k=>$v)
                                <option value="{{ $k }}" @selected(old('status',$service->status)===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control"
                               value="{{ old('display_order', $service->display_order) }}" min="0" step="1">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Excerpt</label>
                        <input type="text" name="excerpt" class="form-control"
                               value="{{ old('excerpt', $service->excerpt) }}" maxlength="500"
                               placeholder="Short summary shown in cards">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" class="form-control"
                               value="{{ old('icon', $service->icon) }}"
                               placeholder="e.g., bx bx-home or /uploads/icons/service.png">
                    </div>
                    <div class="col-md-6 d-flex align-items-center pt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="is_featured" name="is_featured"
                                   value="1" @checked(old('is_featured', $service->is_featured))>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Details</h5>
                </div>
                <div class="card-body">
                    <textarea name="body" class="form-control tinymce"
                              rows="10">{{ old('body', $service->body) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('voyager.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Service</button>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    {{-- Relies on your global TinyMCE / editor initializer --}}
@endsection
