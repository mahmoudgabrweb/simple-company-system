@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">الدفعات</h4>
            <a class="btn btn-primary" href="{{ route('voyager.payments.create') }}">+ إضافة دفعة</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">المشروع</label>
                    <select name="project_id" class="form-select">
                        <option value="">— الكل —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(request('project_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">طريقة الدفع</label>
                    <select name="payment_method" class="form-select">
                        <option value="">— الكل —</option>
                        @foreach($methods as $k=>$v)
                            <option value="{{ $k }}" @selected(request('payment_method')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100">تصفية</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>المشروع</th>
                        <th>المبلغ</th>
                        <th>الطريقة</th>
                        <th>من دافع؟</th>
                        <th>المستلم</th>
                        <th>التاريخ</th>
                        <th>مرفق</th>
                        <th width="220">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $pay)
                        <tr>
                            <td>{{ $pay->id }}</td>
                            <td>{{ $pay->project->name ?? '—' }}</td>
                            <td>{{ number_format($pay->amount, 2) }}</td>
                            <td>
                                @php $label = ['cash'=>'نقدًا','transfer'=>'تحويل','cheque'=>'شيك','card'=>'بطاقة'][$pay->payment_method] ?? $pay->payment_method; @endphp
                                <span class="badge bg-light text-dark">{{ $label }}</span>
                            </td>
                            <td>{{ $pay->paid_by ?? '—' }}</td>
                            <td>{{ $pay->receiver->name ?? '—' }}</td>
                            <td>{{ $pay->paid_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($pay->attachment_path)
                                    <a href="{{ route('admin.payments.attachment', $pay->id) }}"
                                       class="btn btn-sm btn-outline-secondary">تنزيل</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @can('edit', app(\App\Models\Payment::class))
                                        <a href="{{ route('voyager.payments.edit',$pay->id) }}" class="btn btn-warning">تعديل</a>
                                    @endcan
                                    @can('delete', app(\App\Models\Payment::class))
                                        <form action="{{ route('voyager.payments.destroy',$pay->id) }}" method="post"
                                              onsubmit="return confirm('حذف الدفعة؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger">حذف</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">لا توجد دفعات.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">{{ $payments->links() }}</div>
    </div>
@endsection
