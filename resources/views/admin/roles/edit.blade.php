@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header">تعديل الدور</h5>
                    <div class="card-body">
                        <form method="POST" action="{{ route("voyager.roles.update", $role->id) }}" class="row g-4">
                            @csrf
                            @method('PUT')

                            {{-- Basic info --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">اسم النظام (name)
                                    <span class="text-danger"> *</span>
                                </label>
                                <input id="name" name="name" type="text" class="form-control"
                                    value="{{ old('name', $role->name) }}" required>
                                <small class="text-muted">أحرف إنجليزية/أرقام/شرطة سفلية فقط</small>
                                @error('name')
                                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="display_name" class="form-label">اسم العرض</label>
                                <input id="display_name" name="display_name" type="text" class="form-control"
                                    value="{{ old('display_name', $role->display_name) }}">
                                @error('display_name')
                                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (array_key_exists('is_active', $role->getAttributes()))
                                <div class="col-md-6">
                                    <label class="form-label d-block">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            {{ old('is_active', $role->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">نشط</label>
                                    </div>
                                </div>
                            @endif

                            {{-- Permissions toolbar --}}
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <h6 class="mb-0">الصلاحيات</h6>
                                    <button type="button" id="perm-select-all" class="btn btn-sm btn-primary">تحديد
                                        الكل</button>
                                    <button type="button" id="perm-unselect-all"
                                        class="btn btn-sm btn-outline-secondary">إلغاء التحديد</button>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            حسب النوع
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach (['browse', 'read', 'add', 'edit', 'delete'] as $act)
                                                <li><a class="dropdown-item action-select" data-action="{{ $act }}"
                                                        href="#">تحديد {{ $act }}</a></li>
                                                <li><a class="dropdown-item action-unselect"
                                                        data-action="{{ $act }}" href="#">إلغاء
                                                        {{ $act }}</a></li>
                                                @if (!$loop->last)
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="ms-auto" style="min-width: 260px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                                            <input type="text" id="perm-search" class="form-control"
                                                placeholder="ابحث بالجدول أو المفتاح">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Permissions grid --}}
                            @php
                                /** @var \Illuminate\Support\Collection $permissions */
                                $rolePermIds = $role->permissions->pluck('id')->all();
                                $groups = $permissions->groupBy(fn($p) => $p->table_name ?: 'أخرى')->sortKeys();
                            @endphp

                            <div class="col-12">
                                @foreach ($groups as $table => $perms)
                                    <div class="border rounded p-3 mb-3 permission-group" data-group="{{ $table }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>{{ $table }}</strong>
                                                <small class="text-muted ms-2">{{ $perms->count() }} صلاحية</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-group-select"
                                                    data-target="{{ $table }}">
                                                    تحديد الكل
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-group-unselect"
                                                    data-target="{{ $table }}">
                                                    إلغاء التحديد
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            @foreach ($perms as $perm)
                                                @php $action = \Illuminate\Support\Str::before($perm->key, '_'); @endphp
                                                <div class="col-md-6 col-xl-4 mb-2 perm-item"
                                                    data-key="{{ $perm->key }}">
                                                    <div class="form-check">
                                                        <input class="form-check-input perm-checkbox" type="checkbox"
                                                            name="permissions[]" id="perm_{{ $perm->id }}"
                                                            value="{{ $perm->id }}" data-group="{{ $table }}"
                                                            data-action="{{ $action }}"
                                                            {{ in_array($perm->id, $rolePermIds) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                                            <strong>{{ $perm->key }}</strong>
                                                            <small
                                                                class="text-muted d-block">{{ $perm->table_name ?: '—' }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions --}}
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">تحديث</button>
                                <a href="{{ route("voyager.roles.index-new") }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_scripts')
    <script>
        // Global select/unselect
        document.getElementById('perm-select-all')?.addEventListener('click', () => {
            document.querySelectorAll('.perm-checkbox').forEach(ch => ch.checked = true);
        });
        document.getElementById('perm-unselect-all')?.addEventListener('click', () => {
            document.querySelectorAll('.perm-checkbox').forEach(ch => ch.checked = false);
        });

        // Group select/unselect
        document.querySelectorAll('.btn-group-select').forEach(btn => {
            btn.addEventListener('click', function() {
                const g = this.dataset.target;
                document.querySelectorAll('.perm-checkbox[data-group="' + g + '"]').forEach(ch => ch
                    .checked = true);
            });
        });
        document.querySelectorAll('.btn-group-unselect').forEach(btn => {
            btn.addEventListener('click', function() {
                const g = this.dataset.target;
                document.querySelectorAll('.perm-checkbox[data-group="' + g + '"]').forEach(ch => ch
                    .checked = false);
            });
        });

        // Action-based select/unselect
        document.querySelectorAll('.action-select').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.action;
                document.querySelectorAll('.perm-checkbox[data-action="' + action + '"]').forEach(ch => ch
                    .checked = true);
            });
        });
        document.querySelectorAll('.action-unselect').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.action;
                document.querySelectorAll('.perm-checkbox[data-action="' + action + '"]').forEach(ch => ch
                    .checked = false);
            });
        });

        // Search
        const search = document.getElementById('perm-search');
        if (search) {
            search.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                document.querySelectorAll('.permission-group').forEach(group => {
                    let groupMatch = (group.dataset.group || '').toLowerCase().includes(q);
                    let anyVisible = false;

                    group.querySelectorAll('.perm-item').forEach(item => {
                        const key = (item.dataset.key || '').toLowerCase();
                        const show = groupMatch || key.includes(q);
                        item.style.display = show ? '' : 'none';
                        if (show) anyVisible = true;
                    });

                    group.style.display = anyVisible ? '' : 'none';
                });
            });
        }
    </script>
@stop
