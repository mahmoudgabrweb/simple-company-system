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
            <h4 class="m-0">Home Hero Section</h4>
            <a href="{{ route('voyager.home_heroes.index') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Alerts --}}
        @if(session('success') || session('message'))
            <div class="alert alert-success">{{ session('success') ?? session('message') }}</div>
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

        <form action="{{ route('voyager.home_heroes.update', $hero->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Content --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Hero Content</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kicker (small text)</label>
                        <input type="text" name="kicker" class="form-control"
                               value="{{ old('kicker', $hero->kicker) }}" maxlength="120"
                               placeholder="inspired interiors">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label required">Headline</label>
                        <input type="text" name="headline" class="form-control"
                               value="{{ old('headline', $hero->headline) }}" maxlength="190" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Subheadline</label>
                        <input type="text" name="subheadline" class="form-control"
                               value="{{ old('subheadline', $hero->subheadline) }}" maxlength="255"
                               placeholder="Designing your dream spaces, one room at a time">
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Call to Actions</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Primary Text</label>
                        <input type="text" name="primary_cta_text" class="form-control"
                               value="{{ old('primary_cta_text', $hero->primary_cta_text) }}" maxlength="80"
                               placeholder="Get Our Portfolio">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Primary URL</label>
                        <input type="text" name="primary_cta_url" class="form-control"
                               value="{{ old('primary_cta_url', $hero->primary_cta_url) }}" maxlength="255"
                               placeholder="#portfolio">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Secondary Text</label>
                        <input type="text" name="secondary_cta_text" class="form-control"
                               value="{{ old('secondary_cta_text', $hero->secondary_cta_text) }}" maxlength="80"
                               placeholder="Contact Us">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Secondary URL</label>
                        <input type="text" name="secondary_cta_url" class="form-control"
                               value="{{ old('secondary_cta_url', $hero->secondary_cta_url) }}" maxlength="255"
                               placeholder="#contact">
                    </div>
                </div>
            </div>

            {{-- Background --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Background</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-3">
                        <label class="form-label required">Type</label>
                        <select name="background_type" class="form-select" required>
                            @foreach(['image'=>'Image','color'=>'Color','video'=>'Video'] as $k=>$v)
                                <option value="{{ $k }}" @selected(old('background_type', $hero->background_type)===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Value</label>
                        <input type="text" name="background_value" class="form-control"
                               value="{{ old('background_value', $hero->background_value) }}" maxlength="255"
                               placeholder="/uploads/home_bg.jpg or #ffffff or https://vimeo.com/...">
                    </div>
                    <div class="col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                    @checked(old('is_active', $hero->is_active))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('voyager.home_heroes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Hero</button>
            </div>
        </form>
    </div>
@endsection
