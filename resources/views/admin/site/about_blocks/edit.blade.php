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
            <h4 class="m-0">About Section</h4>
            <a href="{{ route('voyager.about_blocks.index') }}" class="btn btn-secondary">Back</a>
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

        <form action="{{ route('voyager.about_blocks.update', $about->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Content</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Title</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $about->title) }}" maxlength="190" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Video URL</label>
                        <input type="text" name="video_url" class="form-control"
                               value="{{ old('video_url', $about->video_url) }}" maxlength="255"
                               placeholder="https://player.vimeo.com/… or https://youtube.com/watch?v=…">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea name="body" class="form-control tinymce"
                                  rows="10">{{ old('body', $about->body) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('voyager.about_blocks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save About</button>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    {{-- Uses your global TinyMCE initializer (e.g., resources/js/custom/tinymce.js) --}}
@endsection
