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

        .image-row, .feature-row {
            border-bottom: 1px solid #eee;
            padding-bottom: .75rem;
            margin-bottom: .75rem;
        }

        .remove-btn {
            cursor: pointer;
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Project</h4>
            <a href="{{ route('voyager.site_projects.index') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Alerts --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('voyager.site_projects.store') }}" method="POST">
            @csrf

            {{-- Basic Info --}}
            <div class="card form-card mb-3">
                <div class="card-header">
                    <h5 class="m-0">Basic Info</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" maxlength="190"
                               required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['published'=>'Published','draft'=>'Draft','archived'=>'Archived'] as $k=>$v)
                                <option value="{{ $k }}" @selected(old('status','published')===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control"
                               value="{{ old('display_order',0) }}" min="0">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Excerpt</label>
                        <input type="text" name="excerpt" class="form-control" value="{{ old('excerpt') }}"
                               maxlength="500">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <input type="text" name="client" value="{{ old('client') }}" class="form-control"
                               maxlength="190">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" value="{{ old('duration') }}" class="form-control"
                               maxlength="190">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="form-control"
                               maxlength="190">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Cover Image</label>
                        <input type="text" name="cover_image" class="form-control" value="{{ old('cover_image') }}"
                               placeholder="/uploads/projects/cover.jpg">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   value="1" @checked(old('is_featured'))>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="card form-card mb-3">
                <div class="card-header"><h5 class="m-0">Details</h5></div>
                <div class="card-body">
                    <textarea name="body" class="form-control tinymce" rows="10">{{ old('body') }}</textarea>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="card form-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Gallery Images</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addImageRow">
                        <i class="bx bx-plus"></i> Add Image
                    </button>
                </div>
                <div class="card-body" id="imagesContainer">
                    {{-- dynamically added --}}
                </div>
            </div>

            {{-- Features --}}
            <div class="card form-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Key Features</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addFeatureRow">
                        <i class="bx bx-plus"></i> Add Feature
                    </button>
                </div>
                <div class="card-body" id="featuresContainer">
                    {{-- dynamically added --}}
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('voyager.site_projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imgContainer = document.getElementById('imagesContainer');
            const featureContainer = document.getElementById('featuresContainer');

            document.getElementById('addImageRow').addEventListener('click', function () {
                const idx = imgContainer.children.length;
                imgContainer.insertAdjacentHTML('beforeend', `
            <div class="image-row row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Image Path</label>
                    <input type="text" name="images[${idx}][image_path]" class="form-control" placeholder="/uploads/projects/img.jpg">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Caption</label>
                    <input type="text" name="images[${idx}][caption]" class="form-control" maxlength="255">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="images[${idx}][display_order]" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-1 text-end">
                    <label class="form-label">Cover</label>
                    <input type="checkbox" name="images[${idx}][is_cover]" value="1" class="form-check-input">
                </div>
                <div class="col-12 text-end">
                    <span class="remove-btn small">Remove</span>
                </div>
            </div>
        `);
            });

            document.getElementById('addFeatureRow').addEventListener('click', function () {
                const idx = featureContainer.children.length;
                featureContainer.insertAdjacentHTML('beforeend', `
            <div class="feature-row row g-2 align-items-end">
                <div class="col-md-10">
                    <label class="form-label">Feature</label>
                    <input type="text" name="features[${idx}][label]" class="form-control" placeholder="e.g., Sustainable materials">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="features[${idx}][display_order]" class="form-control" value="0" min="0">
                </div>
                <div class="col-12 text-end">
                    <span class="remove-btn small">Remove</span>
                </div>
            </div>
        `);
            });

            document.body.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-btn')) {
                    e.target.closest('.image-row, .feature-row').remove();
                }
            });
        });
    </script>
@endsection
