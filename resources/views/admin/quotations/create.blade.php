@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">إنشاء عرض جديد</h4>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.quotations.store') }}" method="post" class="card p-3">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">الشركة</label>
                    <input type="text" class="form-control"
                           value="{{ $currentCompany->name ?? 'سيتم الحفظ على الشركة الحالية' }}" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label">المشروع</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">— اختر المشروع —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">رقم العرض</label>
                    <input name="quotation_number" class="form-control">
                </div>
                <div class="col-md-1">
                    <label class="form-label">نسخة</label>
                    <input type="number" min="1" name="version" class="form-control" value="1">
                </div>
                <div class="col-md-1">
                    <label class="form-label">مفعل؟</label>
                    <select name="is_active" class="form-select">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">حفظ وإنشاء</button>
                <a class="btn btn-secondary" href="{{ route('voyager.quotations.index') }}">رجوع</a>
            </div>
        </form>
    </div>
@endsection
