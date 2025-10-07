@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}"/>
    <style>
        .select2-container {
            width: 100% !important
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            display: flex;
            align-items: center;
            background: #fff
        }

        .select2-container .select2-selection--multiple {
            min-height: 38px;
            padding: .25rem .375rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            background: #fff
        }

        .select2-selection__rendered {
            line-height: 1.5 !important
        }

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important
        }

        .opacity-50 {
            opacity: .5
        }

        .grid-sm {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: .5rem
        }

        .grid-sm .col-span-2 {
            grid-column: span 2 / span 2
        }

        .grid-sm .col-span-3 {
            grid-column: span 3 / span 3
        }

        .grid-sm .col-span-4 {
            grid-column: span 4 / span 4
        }

        .grid-sm .col-span-6 {
            grid-column: span 6 / span 6
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">تعديل وظيفة</h4>
            <a href="{{ route('voyager.jobs.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.jobs.update', $job->id) }}" method="post" id="job-form">
            @csrf
            @method('PUT') {{-- Voyager عادةً يستخدم PUT للتحديث --}}

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ================== معلومات عامة ================== --}}
            <div class="card p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">العنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $job->title) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">نشط؟</label>
                        <select name="is_active" class="form-select">
                            <option value="1" @selected(old('is_active', (int)$job->is_active) === 1)>نعم</option>
                            <option value="0" @selected(old('is_active', (int)$job->is_active) === 0)>لا</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">الشركة</label>
                        <input type="text" class="form-control"
                               value="{{ $currentCompany->name ?? 'الشركة الحالية من السياق' }}"
                               disabled>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12 small text-muted">
                        تم إنشاء السجل: {{ optional($job->created_at)->format('Y-m-d H:i') ?? '—' }} —
                        آخر تعديل: {{ optional($job->updated_at)->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>
            </div>

            {{-- ================== الوصف ================== --}}
            <div class="card p-3 mb-3">
                <label class="form-label mb-2">الوصف</label>
                <textarea name="description" rows="8" class="form-control"
                          dir="rtl">{{ old('description', $job->description) }}</textarea>
                <div class="form-text opacity-50 mt-1">يمكنك تركه فارغاً.</div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">
                    <i class="bx bx-check"></i> حفظ التغييرات
                </button>
                <a href="{{ route('voyager.jobs.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        if ($.fn.select2) {
            $.fn.select2.defaults.set('theme', 'bootstrap-5');
            $.fn.select2.defaults.set('dir', 'rtl');
            $.fn.select2.defaults.set('width', '100%');
            $.fn.select2.defaults.set('placeholder', ' — ');
            $.fn.select2.defaults.set('allowClear', true);
        }

        $(function () {
            $('select.form-select').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
@endsection
