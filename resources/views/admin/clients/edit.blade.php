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
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">تعديل عميل</h4>
            <a href="{{ route('voyager.clients.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.clients.update', $client->id) }}" method="post">
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
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}"
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}"
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">هاتف بديل</label>
                        <input type="text" name="alternative_phone" class="form-control"
                               value="{{ old('alternative_phone', $client->alternative_phone) }}">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $client->email) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">المدينة <span class="text-danger">*</span></label>
                        <select name="city_id" class="form-select" required>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}" @selected(old('city_id', $client->city_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الشركة</label>
                        <input type="text" class="form-control"
                               value="{{ $currentCompany->name ?? 'الشركة الحالية من السياق' }}" disabled>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">خريطة جوجل (رابط/تضمين)</label>
                        <textarea name="map" rows="3" class="form-control"
                                  dir="ltr">{{ old('map', $client->map) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">العنوان</label>
                        <textarea name="address" rows="3" class="form-control"
                                  dir="rtl">{{ old('address', $client->address) }}</textarea>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12 small text-muted">
                        تم الإنشاء: {{ optional($client->created_at)->format('Y-m-d H:i') ?? '—' }} —
                        آخر تعديل: {{ optional($client->updated_at)->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('voyager.clients.index') }}" class="btn btn-secondary">رجوع</a>
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
