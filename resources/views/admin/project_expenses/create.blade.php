@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">إضافة مصروف</h4>
            <a href="{{ route('voyager.project_expenses.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        <form action="{{ route('voyager.project_expenses.store') }}" method="post" class="card p-3">
            @csrf
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">المشروع</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">— اختر المشروع —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">المبلغ</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                           value="{{ old('amount') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">نوع الدفع</label>
                    <select name="payment_type" class="form-select" required>
                        @foreach($types as $k=>$v)
                            <option value="{{ $k }}" @selected(old('payment_type')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">التاريخ</label>
                    <input type="datetime-local" name="paid_at" class="form-control" value="{{ old('paid_at') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">مرفق (إيصال/صورة/PDF)</label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.webp">
                </div>

                <div class="col-md-6">
                    <label class="form-label">الدافع (موظف)</label>
                    <select name="paid_by_employee_id" class="form-select">
                        <option value="">—</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" @selected(old('paid_by_employee_id')==$e->id)>{{ $e->name }}
                                — {{ $e->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">حفظ</button>
                <a href="{{ route('voyager.project_expenses.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>
    </div>
@endsection
