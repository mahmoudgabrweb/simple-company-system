@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">العروض</h4>
            <a href="{{ route('voyager.quotations.create') }}" class="btn btn-primary">+ عرض جديد</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>المشروع</th>
                        <th>الكود</th>
                        <th>نسخة</th>
                        <th>الحالة</th>
                        <th>مفعل؟</th>
                        <th>الإجمالي</th>
                        <th style="width:280px">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($quotations as $q)
                        <tr>
                            <td>{{ $q->id }}</td>
                            <td>{{ optional($q->project)->name }}</td>
                            <td>{{ $q->code ?? '—' }}</td>
                            <td>{{ $q->version }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $q->status }}</span>
                            </td>
                            <td>
                                @if($q->is_active)
                                    <span class="badge bg-success">نعم</span>
                                @else
                                    <span class="badge bg-light text-dark">لا</span>
                                @endif
                            </td>
                            <td>{{ number_format($q->grand_total, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-info"
                                       href="{{ route('voyager.quotations.show', $q->id) }}">عرض</a>
                                    <a class="btn btn-warning" href="{{ route('voyager.quotations.edit', $q->id) }}">تعديل</a>

                                    <form action="{{ route('admin.quotations.resend', $q->id) }}" method="post"
                                          onsubmit="return confirm('إعادة إرسال العرض للعميل؟')">
                                        @csrf
                                        <button class="btn btn-outline-primary" type="submit">إعادة إرسال</button>
                                    </form>

                                    <form action="{{ route('voyager.quotations.destroy', $q->id) }}" method="post"
                                          onsubmit="return confirm('حذف العرض؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" type="submit">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد عروض حتى الآن.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $quotations->links() }}</div>
    </div>
@endsection
