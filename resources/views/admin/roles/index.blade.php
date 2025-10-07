@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        <span>الأدوار</span>
                        <a href="{{ url("/admin/roles/create-new") }}" class="btn btn-success">إضافة دور</a>
                    </h5>

                    <div class="card-body">
                        {{-- Flash --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Filters --}}
                        {{-- <form method="GET" action="{{ route('voyager.roles.index-new') }}" class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم / اسم العرض</label>
                                <input type="text" name="q" class="form-control" placeholder="ابحث..."
                                    value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط
                                    </option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير
                                        نشط</option>
                                </select>
                                <small class="text-muted">يُطبق فقط إذا كان لدى جدول الأدوار حقل is_active</small>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">تطبيق</button>
                                <a href="{{ route('voyager.roles.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                            </div>
                        </form> --}}

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم (name)</th>
                                        <th>اسم العرض</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th class="text-end">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->display_name ?? '—' }}</td>
                                            <td>
                                                @if (array_key_exists('is_active', $role->getAttributes()))
                                                    @if ($role->is_active)
                                                        <span class="badge bg-label-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">غير نشط</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($role->created_at)->format('Y-m-d H:i') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('voyager.roles.edit', $role->id) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                @if ($role->name !== 'admin')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-url="{{ route('voyager.roles.destroy', $role->id) }}">
                                                        حذف
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $roles->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_scripts')
    <script>
        // Delete (uses native confirm; switch to jquery-confirm if you prefer)
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('هل أنت متأكد من حذف هذا الدور؟')) return;
                const url = this.dataset.url;
                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.ok ? location.reload() : r.json().then(j => Promise.reject(j)))
                    .catch(() => alert('تعذر حذف الدور'));
            });
        });
    </script>
@stop
