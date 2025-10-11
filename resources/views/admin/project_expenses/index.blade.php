@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">مصروفات المشاريع</h4>
            <a class="btn btn-primary" href="{{ route('voyager.project_expenses.create') }}">+ إضافة مصروف</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
                    <label class="form-label">نوع الدفع</label>
                    <select name="payment_type" class="form-select">
                        <option value="">— الكل —</option>
                        @foreach($types as $k=>$v)
                            <option value="{{ $k }}" @selected(request('payment_type')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100">تصفية</button>
                </div>
            </div>
        </form>

        @if(isset($sumAll))
            <div class="card p-3 mb-3">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="fw-bold">إجمالي المصروفات (حسب التصفية): {{ number_format($sumAll, 2) }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($sumTypes as $t => $total)
                            <span class="badge bg-light text-dark">
            {{ $paymentsMap[$t] ?? $t }}: {{ number_format($total, 2) }}
          </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>المشروع</th>
                        <th>العنوان</th>
                        <th>المبلغ</th>
                        <th>النوع</th>
                        <th>الدافع (موظف)</th>
                        <th>التاريخ</th>
                        <th>مرفق</th>
                        <th width="220">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->project->name ?? '—' }}</td>
                            <td>{{ $e->title }}</td>
                            <td>{{ number_format($e->amount,2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $types[$e->payment_type] ?? $e->payment_type }}</span>
                            </td>
                            <td>{{ $e->payer->name ?? '—' }}</td>
                            <td>{{ $e->paid_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($e->attachment_path)
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="{{ route('admin.project_expenses.attachment', $e->id) }}">تنزيل</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @can('edit', app(\App\Models\ProjectExpense::class))
                                        <a href="{{ route('voyager.project_expenses.edit', $e->id) }}"
                                           class="btn btn-warning">تعديل</a>
                                    @endcan
                                    @can('delete', app(\App\Models\ProjectExpense::class))
                                        <form action="{{ route('voyager.project_expenses.destroy', $e->id) }}"
                                              method="post" onsubmit="return confirm('حذف المصروف؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger">حذف</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد مصروفات.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">{{ $expenses->links() }}</div>
    </div>
@endsection
