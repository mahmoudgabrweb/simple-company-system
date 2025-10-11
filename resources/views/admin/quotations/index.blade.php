@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">العروض</h4>
            <a class="btn btn-primary" href="{{ route('voyager.quotations.create') }}">+ عرض جديد</a>
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
                        <th>الرقم</th>
                        <th>نسخة</th>
                        <th>الحالة</th>
                        <th>مفعل؟</th>
                        <th>الإجمالي</th>
                        <th width="280">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($quotations as $q)
                        <tr>
                            <td>{{ $q->id }}</td>
                            <td>{{ $q->project->name ?? '—' }}</td>
                            <td>{{ $q->quotation_number ?? '—' }}</td>
                            <td>{{ $q->version }}</td>
                            <td><span class="badge bg-secondary">{{ $q->status }}</span></td>
                            <td>@if($q->is_active)
                                    <span class="badge bg-success">نعم</span>
                                @else
                                    <span class="badge bg-light text-dark">لا</span>
                                @endif</td>
                            <td>{{ number_format($q->total_amount,2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('voyager.quotations.edit',$q->id) }}"
                                       class="btn btn-warning">تعديل</a>
                                    <form action="{{ route('admin.quotations.resend',$q->id) }}" method="post"
                                          onsubmit="return confirm('إرسال للعميل؟');">
                                        @csrf
                                        <button class="btn btn-outline-primary">إرسال</button>
                                    </form>
                                    <form action="{{ route('voyager.quotations.destroy',$q->id) }}" method="post"
                                          onsubmit="return confirm('حذف العرض؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد عروض.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-2">{{ $quotations->links() }}</div>
    </div>
@endsection
