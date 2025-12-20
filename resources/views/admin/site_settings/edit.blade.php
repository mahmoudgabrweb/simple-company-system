@extends('admin.main')

@section('css_sheets')
    <style>
        .setting-card .card-header {
            background: #f8f9fa;
        }
        .setting-card .current-file img {
            max-height: 90px;
            border-radius: 6px;
            margin-top: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Site Settings</h4>
            <a href="{{ route('voyager.dashboard') }}" class="btn btn-secondary">Back</a>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.site_settings.update') }}"
              method="POST"
              enctype="multipart/form-data"
              id="settings-form">

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

            {{-- Actions --}}
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" id="add-setting-btn">+ Add Setting</button>
            </div>

            <div id="settings-wrapper">

                @foreach($settings as $setting)
                    @php
                        $rowKey = 'id_'.$setting->id;
                        $fileUrl = ($setting->type === 'file' && $setting->value)
                            ? Storage::disk('public')->url($setting->value)
                            : null;
                    @endphp

                    <div class="card p-3 mb-3 setting-card"
                         data-id="{{ $setting->id }}"
                         data-rowkey="{{ $rowKey }}"
                         data-prev-type="{{ $setting->type }}">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Setting</div>
                            <button type="button" class="btn btn-outline-danger btn-sm delete-setting-btn">
                                Delete
                            </button>
                        </div>

                        <input type="hidden" name="settings[{{ $rowKey }}][id]" value="{{ $setting->id }}">
                        <input type="hidden"
                               name="settings[{{ $rowKey }}][switched_to_file]"
                               class="switched-to-file"
                               value="0">

                        <div class="row g-3">

                            {{-- Key --}}
                            <div class="col-md-4">
                                <label class="form-label">Key <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="settings[{{ $rowKey }}][key]"
                                       class="form-control key-input"
                                       value="{{ old("settings.$rowKey.key", $setting->key) }}"
                                       required>
                            </div>

                            {{-- Type --}}
                            <div class="col-md-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="settings[{{ $rowKey }}][type]"
                                        class="form-select type-select"
                                        required>
                                    <option value="text" {{ $setting->type==='text'?'selected':'' }}>text</option>
                                    <option value="longtext" {{ $setting->type==='longtext'?'selected':'' }}>longtext</option>
                                    <option value="file" {{ $setting->type==='file'?'selected':'' }}>file</option>
                                </select>
                            </div>

                            {{-- Value --}}
                            <div class="col-md-5 value-holder" data-type="{{ $setting->type }}">
                                <label class="form-label">Value</label>

                                {{-- TEXT --}}
                                <input type="text"
                                       class="form-control value-text"
                                       name="settings[{{ $rowKey }}][value]"
                                       value="{{ $setting->type==='text' ? old("settings.$rowKey.value", $setting->value) : '' }}"
                                       style="{{ $setting->type==='text' ? '' : 'display:none;' }}"
                                        {{ $setting->type==='text' ? '' : 'disabled' }}>

                                {{-- LONGTEXT --}}
                                <textarea rows="3"
                                          class="form-control value-longtext"
                                          name="settings[{{ $rowKey }}][value]"
                                          style="{{ $setting->type==='longtext' ? '' : 'display:none;' }}"
                                      {{ $setting->type==='longtext' ? '' : 'disabled' }}>{{ $setting->type==='longtext' ? old("settings.$rowKey.value", $setting->value) : '' }}</textarea>

                                {{-- FILE --}}
                                <div class="value-file" style="{{ $setting->type==='file' ? '' : 'display:none;' }}">
                                    <input type="file"
                                           class="form-control file-input"
                                           name="settings[{{ $rowKey }}][file]"
                                            {{ $setting->type==='file' ? '' : 'disabled' }}>

                                    <input type="hidden"
                                           class="existing-file-path"
                                           name="settings[{{ $rowKey }}][value]"
                                           value="{{ $setting->type==='file' ? $setting->value : '' }}"
                                            {{ $setting->type==='file' ? '' : 'disabled' }}>

                                    @if($fileUrl)
                                        <div class="current-file mt-2">
                                            <div class="small text-muted mb-1">Current file:</div>
                                            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $setting->value))
                                                <img src="{{ $fileUrl }}" alt="file">
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank">Open file</a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Buttons --}}
            <div class="mt-3">
                <button class="btn btn-primary">Update</button>
                <a href="{{ route('voyager.dashboard') }}" class="btn btn-secondary">Back</a>
            </div>

        </form>

        {{-- TEMPLATE --}}
        <template id="setting-template">
            <div class="card p-3 mb-3 setting-card" data-prev-type="text">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">New Setting</div>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-setting-btn">Delete</button>
                </div>

                <input type="hidden" name="settings[__ROWKEY__][switched_to_file]" class="switched-to-file" value="0">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Key <span class="text-danger">*</span></label>
                        <input type="text" name="settings[__ROWKEY__][key]" class="form-control key-input" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="settings[__ROWKEY__][type]" class="form-select type-select" required>
                            <option value="text" selected>text</option>
                            <option value="longtext">longtext</option>
                            <option value="file">file</option>
                        </select>
                    </div>

                    <div class="col-md-5 value-holder" data-type="text">
                        <label class="form-label">Value</label>

                        <input type="text"
                               class="form-control value-text"
                               name="settings[__ROWKEY__][value]">

                        <textarea rows="3"
                                  class="form-control value-longtext"
                                  name="settings[__ROWKEY__][value]"
                                  style="display:none;"
                                  disabled></textarea>

                        <div class="value-file" style="display:none;">
                            <input type="file"
                                   class="form-control file-input"
                                   name="settings[__ROWKEY__][file]"
                                   disabled>

                            <input type="hidden"
                                   class="existing-file-path"
                                   name="settings[__ROWKEY__][value]"
                                   value=""
                                   disabled>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('assets/js/site_settings.js') }}"></script>
@endsection
