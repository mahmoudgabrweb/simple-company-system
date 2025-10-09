@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">إنشاء عرض جديد</h4>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.quotations.store') }}" method="post">
            @csrf

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
                        <label class="form-label">المشروع</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">— اختر المشروع —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الكود</label>
                        <input type="text" name="code" class="form-control"/>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">النسخة</label>
                        <input type="number" min="1" name="version" class="form-control" value="1"/>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نسبة ضريبة %</label>
                        <input type="number" step="0.01" min="0" max="100" name="vat_rate" class="form-control"
                               value="0"/>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تفعيل العرض؟</label>
                        <select name="is_active" class="form-select">
                            <option value="0">لا</option>
                            <option value="1">نعم</option>
                        </select>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary">حفظ وإنشاء</button>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">رجوع</a>
        </form>
    </div>
@endsection
