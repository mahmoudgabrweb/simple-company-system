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
            <h4 class="m-0">تعديل مصروف</h4>
            <a href="{{ route('voyager.expenses.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        <form action="{{ route('voyager.expenses.update', $expense->id) }}" method="post">
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
                        <label class="form-label">العنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $expense->title) }}"
                               required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control"
                               value="{{ old('amount', $expense->amount) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">تاريخ المصروف</label>
                        <input type="date" name="spent_at" class="form-control"
                               value="{{ old('spent_at', optional($expense->spent_at)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">نوع المصروف <span class="text-danger">*</span></label>
                        <select name="expense_type_id" class="form-select" required>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" @selected(old('expense_type_id', $expense->expense_type_id)==$t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" rows="3" class="form-control"
                                  dir="rtl">{{ old('description', $expense->description) }}</textarea>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-12 small text-muted">
                        أُنشئ بواسطة: {{ $expense->creator->name ?? '—' }} |
                        آخر تعديل: {{ optional($expense->updated_at)->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('voyager.expenses.index') }}" class="btn btn-secondary">رجوع</a>
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
