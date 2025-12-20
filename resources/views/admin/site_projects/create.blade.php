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

        .image-preview {
            max-height: 80px;
            border-radius: 6px;
            margin-top: 8px;
        }

        .repeater-row {
            border: 1px solid #e3e6ea;
            border-radius: .375rem;
            padding: .75rem;
            margin-bottom: .5rem;
            background-color: #f9fafb;
        }

        .repeater-row .btn-outline-danger {
            padding: .15rem .5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Site Project</h4>
            <a href="{{ route('voyager.site_projects.index') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Form --}}
        <form action="{{ route('voyager.site_projects.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

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

            {{-- Basic Info Card --}}
            <div class="card p-3 mb-3">
                <h5 class="card-title mb-3">Basic Information</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <input type="text"
                               name="client"
                               class="form-control"
                               value="{{ old('client') }}"
                               placeholder="Client name (optional)">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <input type="text"
                               name="category"
                               class="form-control"
                               value="{{ old('category') }}"
                               placeholder="e.g. Fit-out, Interior, Renovation">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <input type="text"
                               name="duration"
                               class="form-control"
                               value="{{ old('duration') }}"
                               placeholder="e.g. 6 months, 2023">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check mt-1">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                    {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active?
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Main Image</label>
                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">
                        <small class="text-muted d-block mt-1">Upload a main image for the project (PNG, JPG,
                            WEBP)</small>
                        {{-- Preview can be added via JS if you like --}}
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Short summary shown on cards or listings">{{ old('short_description') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Description</label>
                        <textarea name="full_description"
                                  rows="6"
                                  class="form-control tinymce-editor"
                                  placeholder="Full project description (can contain HTML)">{{ old('full_description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Achievements Card --}}
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title m-0">Project Achievements</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-achievement-row">
                        + Add Achievement
                    </button>
                </div>

                <div id="achievements-container">
                    {{-- Initial Row (optional, empty) --}}
                    <div class="repeater-row achievement-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9">
                                <label class="form-label">Description</label>
                                <textarea name="achievements[0][description]"
                                          rows="2"
                                          class="form-control"
                                          placeholder="Achievement description">{{ old('achievements.0.description') }}</textarea>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-4">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="achievements[0][is_active]"
                                           value="1"
                                           id="achievement-0-active"
                                            {{ old('achievements.0.is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="achievement-0-active">
                                        Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button"
                                        class="btn btn-outline-danger mt-4 btn-remove-achievement"
                                        title="Remove">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sliders Card --}}
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title m-0">Project Sliders</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-slider-row">
                        + Add Slider Image
                    </button>
                </div>

                <div id="sliders-container">
                    {{-- Initial slider row --}}
                    <div class="repeater-row slider-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file"
                                       name="new_sliders[]"
                                       class="form-control"
                                       accept="image/*">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Caption (optional)</label>
                                <input type="text"
                                       name="new_sliders_captions[]"
                                       class="form-control"
                                       placeholder="Caption or short text for this slide">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-4">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="new_sliders_is_active[]"
                                           value="1"
                                           checked
                                           id="slider-0-active">
                                    <label class="form-check-label" for="slider-0-active">
                                        Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button"
                                        class="btn btn-outline-danger mt-4 btn-remove-slider"
                                        title="Remove">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('voyager.site_projects.index') }}" class="btn btn-secondary">Back</a>
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

            // === Achievements Repeater ===
            let achievementIndex = 1; // 0 already used

            $('#add-achievement-row').on('click', function () {
                const idx = achievementIndex++;
                const row = `
                <div class="repeater-row achievement-row">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <label class="form-label">Description</label>
                            <textarea name="achievements[${idx}][description]"
                                      rows="2"
                                      class="form-control"
                                      placeholder="Achievement description"></textarea>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-4">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="achievements[${idx}][is_active]"
                                       value="1"
                                       id="achievement-${idx}-active"
                                       checked>
                                <label class="form-check-label" for="achievement-${idx}-active">
                                    Active
                                </label>
                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button"
                                    class="btn btn-outline-danger mt-4 btn-remove-achievement"
                                    title="Remove">&times;</button>
                        </div>
                    </div>
                </div>`;
                $('#achievements-container').append(row);
            });

            $(document).on('click', '.btn-remove-achievement', function () {
                $(this).closest('.achievement-row').remove();
            });

            // === Sliders Repeater ===
            let sliderIndex = 1; // 0 already used

            $('#add-slider-row').on('click', function () {
                const idx = sliderIndex++;
                const row = `
                <div class="repeater-row slider-row">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label">Image</label>
                            <input type="file"
                                   name="new_sliders[]"
                                   class="form-control"
                                   accept="image/*">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Caption (optional)</label>
                            <input type="text"
                                   name="new_sliders_captions[]"
                                   class="form-control"
                                   placeholder="Caption or short text for this slide">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-4">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="new_sliders_is_active[]"
                                       value="1"
                                       checked
                                       id="slider-${idx}-active">
                                <label class="form-check-label" for="slider-${idx}-active">
                                    Active
                                </label>
                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button"
                                    class="btn btn-outline-danger mt-4 btn-remove-slider"
                                    title="Remove">&times;</button>
                        </div>
                    </div>
                </div>`;
                $('#sliders-container').append(row);
            });

            $(document).on('click', '.btn-remove-slider', function () {
                $(this).closest('.slider-row').remove();
            });
        });
    </script>
@endsection
