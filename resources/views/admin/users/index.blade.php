@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        <span>المستخدمون</span>
                        <a href="{{ route('voyager.users.create-new') }}" class="btn btn-success">إضافة مستخدم</a>
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
                        {{-- <form method="GET" action="{{ route('voyager.users.index') }}" class="row g-4 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">الاسم / البريد</label>
                                <input type="text" name="q" class="form-control" placeholder="ابحث..."
                                    value="{{ request('q') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">الدور</label>
                                <select name="role_id" class="form-control">
                                    <option value="">الكل</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}"
                                            {{ (string) request('role_id') === (string) $r->id ? 'selected' : '' }}>
                                            {{ $r->display_name ?? $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط
                                    </option>
                                </select>
                                <small class="text-muted">يُطبق فقط إذا كان لدى جدول المستخدمين حقل is_active</small>
                            </div>

                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">تطبيق</button>
                                <a href="{{ route('voyager.users.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                            </div>
                        </form> --}}

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>البريد</th>
                                        <th>الدور</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th class="text-end">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $u)
                                        <tr>
                                            <td>{{ $u->id }}</td>
                                            <td>{{ $u->name ?? '—' }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>
                                                @php
                                                    // Support either single role (role/role_id) or many-to-many (roles)
                                                    $roleName = null;
                                                    if (method_exists($u, 'roles')) {
                                                        $roleName =
                                                            $u->roles->pluck('display_name')->filter()->first() ??
                                                            $u->roles->pluck('name')->first();
                                                    } elseif (isset($u->role)) {
                                                        $roleName = $u->role->display_name ?? ($u->role->name ?? null);
                                                    }
                                                @endphp
                                                {{ $roleName ?? '—' }}
                                            </td>
                                            <td>
                                                @if (array_key_exists('is_active', $u->getAttributes()))
                                                    @if ($u->is_active)
                                                        <span class="badge bg-label-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">غير نشط</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($u->created_at)->format('Y-m-d H:i') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('voyager.users.edit', $u->id) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                @if (auth()->id() !== $u->id)
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-url="{{ route('voyager.users.destroy', $u->id) }}">
                                                        حذف
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟')) return;
                const url = this.dataset.url;
                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(r => r.ok ? location.reload() : Promise.reject())
                    .catch(() => alert('تعذر حذف المستخدم'));
            });
        });
    </script>
@stop
