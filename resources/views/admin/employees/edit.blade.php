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

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important
        }

        .thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
            border-radius: .375rem
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">تعديل موظف</h4>
            <a href="{{ route('voyager.employees.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.employees.update', $employee->id) }}" method="post"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="card p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}"
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $employee->phone) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $employee->email) }}">
                    </div>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">الوظيفة <span class="text-danger">*</span></label>
                        <select name="job_id" class="form-select" required>
                            @foreach($jobs as $j)
                                <option value="{{ $j->id }}" @selected(old('job_id', $employee->job_id) == $j->id)>{{ $j->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">تاريخ البدء</label>
                        <input type="date" name="start_at" class="form-control"
                               value="{{ old('start_at', optional($employee->start_at)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">تاريخ الانتهاء</label>
                        <input type="date" name="end_at" class="form-control"
                               value="{{ old('end_at', optional($employee->end_at)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">الراتب</label>
                        <input type="number" step="0.01" min="0" name="salary" class="form-control"
                               value="{{ old('salary', $employee->salary) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">السيرة الذاتية</label>
                        @if($employee->cv_path)
                            <div class="mb-1">
                                <a href="{{ Storage::disk('public')->url($employee->cv_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-info">عرض الملف الحالي</a>
                            </div>
                        @endif
                        <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الصورة</label>
                        <div class="mb-1">
                            @if($employee->image_path)
                                <img src="{{ Storage::disk('public')->url($employee->image_path) }}" class="thumb"
                                     alt="img">
                            @else
                                <span class="text-muted">لا توجد صورة</span>
                            @endif
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12 small text-muted">
                        تم الإنشاء: {{ optional($employee->created_at)->format('Y-m-d H:i') ?? '—' }} —
                        آخر تعديل: {{ optional($employee->updated_at)->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('voyager.employees.index') }}" class="btn btn-secondary">رجوع</a>
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
        }
        $(function () {
            $('select.form-select').select2({minimumResultsForSearch: 10});
        });
    </script>
@endsection
