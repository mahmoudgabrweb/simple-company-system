@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">تعديل عرض #{{ $quotation->id }}</h4>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        {{-- Header --}}
        <form action="{{ route('admin.quotations.update', $quotation->id) }}" method="post" class="card p-3 mb-3">
            @csrf @method('PUT')
            <div class="row g-3 align-items-end">
                <div class="col-md-3"><label class="form-label">المشروع</label><input class="form-control"
                                                                                      value="{{ $quotation->project->name ?? '' }}"
                                                                                      disabled></div>
                <div class="col-md-2"><label class="form-label">الكود</label><input name="code" class="form-control"
                                                                                    value="{{ $quotation->code }}">
                </div>
                <div class="col-md-1"><label class="form-label">نسخة</label><input type="number" min="1" name="version"
                                                                                   class="form-control"
                                                                                   value="{{ $quotation->version }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        @foreach(['draft','sending_to_client','waiting_client_response','client_approved','in_progress','completed'] as $st)
                            <option value="{{ $st }}" @selected($quotation->status===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label">ضريبة %</label><input name="vat_rate" type="number"
                                                                                      step="0.01" min="0" max="100"
                                                                                      class="form-control"
                                                                                      value="{{ $quotation->vat_rate }}">
                </div>
                <div class="col-md-2"><label class="form-label">مفعل؟</label>
                    <select name="is_active" class="form-select">
                        <option value="0" @selected(!$quotation->is_active)>لا</option>
                        <option value="1" @selected($quotation->is_active)>نعم</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">حفظ</button>
                @if($quotation->pdf_path)
                    <a class="btn btn-outline-secondary" href="{{ asset('storage/'.$quotation->pdf_path) }}"
                       target="_blank">عرض PDF</a>
                @endif
                <form class="d-inline" action="{{ route('admin.quotations.resend',$quotation->id) }}" method="post">
                    @csrf
                    <button class="btn btn-outline-primary">إعادة إرسال للعميل</button>
                </form>
            </div>
        </form>

        {{-- Items quick view (you can replace with the full builder later) --}}
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="m-0">العناصر</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddItem">+ عنصر
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                        <th style="width:180px">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $it)
                        <tr>
                            <td>{{ $it->id }}</td>
                            <td>
                                <strong>{{ $it->title }}</strong>
                                @if($it->parent_id)
                                    <div class="text-muted small">تابع للعنصر #{{ $it->parent_id }}</div>
                                @endif
                                @if($it->is_static)
                                    <div class="badge bg-info">ثابت</div>
                                @endif
                            </td>
                            <td>{{ $it->pricing_kind }}</td>
                            <td>
                                @if($it->pricing_kind==='unit')
                                    {{ $it->quantity }}
                                    × {{ number_format($it->unit_price,2) }} {{ optional($it->unit)->code }}
                                @elseif($it->pricing_kind==='lump')
                                    {{ number_format($it->lump_sum,2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ number_format($it->line_total,2) }}</td>
                            <td><span class="badge bg-secondary">{{ $it->item_status }}</span></td>
                            <td>{!! $it->notes ? Str::limit(strip_tags($it->notes), 60) : '—' !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button"
                                            class="btn btn-warning btn-edit-item"
                                            data-id="{{ $it->id }}"
                                            data-title="{{ e($it->title) }}"
                                            data-node_type="{{ $it->node_type }}"
                                            data-pricing_kind="{{ $it->pricing_kind }}"
                                            data-unit_id="{{ $it->unit_id }}"
                                            data-quantity="{{ $it->quantity }}"
                                            data-unit_price="{{ $it->unit_price }}"
                                            data-lump_sum="{{ $it->lump_sum }}"
                                            data-item_status="{{ $it->item_status }}"
                                            data-include_in_total="{{ (int)$it->include_in_total }}"
                                            data-notes='@json($it->notes)'>
                                        تعديل
                                    </button>
                                    <form action="{{ route('voyager.quotations.items.delete', $it->id) }}" method="post"
                                          onsubmit="return confirm('حذف؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">لا توجد عناصر</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Item Modal (TinyMCE-ready notes) --}}
    <div class="modal fade" id="modalAddItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('voyager.quotations.items.store', $quotation->id) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">عنصر جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-8"><label class="form-label">العنوان</label><input name="title"
                                                                                                  class="form-control"
                                                                                                  required></div>
                            <div class="col-md-4">
                                <label class="form-label">نوع العقدة</label>
                                <select name="node_type" class="form-select">
                                    <option value="line">سطر</option>
                                    <option value="section">قسم</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">التسعير</label>
                                <select name="pricing_kind" class="form-select">
                                    <option value="none">بدون</option>
                                    <option value="unit">وحدي</option>
                                    <option value="lump">مقطوع</option>
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">الوحدة</label>
                                <select name="unit_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->name_ar ?? $u->name_en ?? $u->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">الكمية</label><input type="number"
                                                                                                 step="0.001"
                                                                                                 name="quantity"
                                                                                                 class="form-control">
                            </div>
                            <div class="col-md-2"><label class="form-label">سعر الوحدة</label><input type="number"
                                                                                                     step="0.01"
                                                                                                     name="unit_price"
                                                                                                     class="form-control">
                            </div>
                            <div class="col-md-2"><label class="form-label">مبلغ مقطوع</label><input type="number"
                                                                                                     step="0.01"
                                                                                                     name="lump_sum"
                                                                                                     class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="item_status" class="form-select">
                                    @foreach(['included','optional','tbd','excluded'] as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">احتساب</label>
                                <select name="include_in_total" class="form-select">
                                    <option value="1">نعم</option>
                                    <option value="0">لا</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">حفظ</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Item Modal --}}
    <div class="modal fade" id="modalEditItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form-edit-item" method="post">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل عنصر</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" value="">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <label class="form-label">العنوان</label>
                                <input name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نوع العقدة</label>
                                <select name="node_type" class="form-select">
                                    <option value="line">سطر</option>
                                    <option value="section">قسم</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">التسعير</label>
                                <select name="pricing_kind" class="form-select">
                                    <option value="none">بدون</option>
                                    <option value="unit">وحدي</option>
                                    <option value="lump">مقطوع</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الوحدة</label>
                                <select name="unit_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->name_ar ?? $u->name_en ?? $u->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">الكمية</label><input type="number"
                                                                                                 step="0.001"
                                                                                                 name="quantity"
                                                                                                 class="form-control">
                            </div>
                            <div class="col-md-2"><label class="form-label">سعر الوحدة</label><input type="number"
                                                                                                     step="0.01"
                                                                                                     name="unit_price"
                                                                                                     class="form-control">
                            </div>
                            <div class="col-md-2"><label class="form-label">مبلغ مقطوع</label><input type="number"
                                                                                                     step="0.01"
                                                                                                     name="lump_sum"
                                                                                                     class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="item_status" class="form-select">
                                    @foreach(['included','optional','tbd','excluded'] as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">احتساب</label>
                                <select name="include_in_total" class="form-select">
                                    <option value="1">نعم</option>
                                    <option value="0">لا</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">حفظ</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- TinyMCE (optional – enable if you want rich notes inline) --}}
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        (function () {
            // open modal with row data
            $(document).on('click', '.btn-edit-item', function () {
                var b = $(this);
                var id = b.data('id');
                var m = $('#modalEditItem');

                m.find('[name="item_id"]').val(id);
                m.find('[name="title"]').val(b.data('title'));
                m.find('[name="node_type"]').val(b.data('node_type'));
                m.find('[name="pricing_kind"]').val(b.data('pricing_kind'));
                m.find('[name="unit_id"]').val(b.data('unit_id') || '');
                m.find('[name="quantity"]').val(b.data('quantity') || '');
                m.find('[name="unit_price"]').val(b.data('unit_price') || '');
                m.find('[name="lump_sum"]').val(b.data('lump_sum') || '');
                m.find('[name="item_status"]').val(b.data('item_status'));
                m.find('[name="include_in_total"]').val(String(b.data('include_in_total') || 0));

                // notes (TinyMCE)
                var notes = b.data('notes') || '';
                if (window.tinymce && tinymce.get('editNotes')) {
                    tinymce.get('editNotes').setContent(notes);
                } else {
                    m.find('textarea[name="notes"]').val(notes);
                }

                // set form action to PUT endpoint
                var action = "{{ route('voyager.quotations.items.update', '__ID__') }}".replace('__ID__', id);
                m.find('form#form-edit-item').attr('action', action);

                var inst = new bootstrap.Modal(document.getElementById('modalEditItem'));
                inst.show();
            });

            // optional: init TinyMCE for edit modal too
            if (window.tinymce) {
                tinymce.init({
                    selector: '#modalEditItem textarea[name="notes"]',
                    menubar: false,
                    plugins: 'lists link table code',
                    toolbar: 'undo redo | styles | bold italic underline | bullist numlist | link table | code',
                    directionality: 'rtl'
                });
            }
        })();
    </script>
@endpush
