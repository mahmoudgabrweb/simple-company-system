@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Variation #{{ $variation->id }}</h4>
            <a href="{{ route('voyager.projects.variations.index', $project->id) }}" class="btn btn-secondary">Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        {{-- Header (same card look as quotations) --}}
        <form action="{{ route('voyager.projects.variations.update', [$project->id, $variation->id]) }}"
              method="post" class="card p-3 mb-3">
            @csrf @method('PUT')

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <input class="form-control" value="{{ $project->name ?? '' }}" disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input name="title" class="form-control" value="{{ old('title', $variation->title) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    @php $statuses = ['draft','sent','accepted','rejected','expired','completed']; @endphp
                    <select name="status" class="form-select">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" @selected(old('status',$variation->status)===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" maxlength="3" class="form-control"
                           value="{{ old('currency', $variation->currency ?? 'AED') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until"
                           value="{{ old('valid_until', $variation->valid_until?->format('Y-m-d')) }}"
                           class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes"
                              class="form-control tinymce-editor">{{ old('notes', $variation->notes) }}</textarea>
                </div>
            </div>

            {{-- Sections + Items (same visual style as quotation, but JS-managed) --}}
            <div class="mt-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="m-0">Sections & Items</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddSection">+ Add Section
                    </button>
                </div>

                <div id="sectionsContainer">
                    {{-- Prefill JSON from model for edit --}}
                    <script id="prefill-sections" type="application/json">
                        {!! json_encode(
                            $variation->sections->map(fn($s)=>[
                                'id'    => $s->id,   // optional, in case you track existing ids
                                'title' => $s->title,
                                'order' => $s->order,
                                'description' => $s->description,
                                'items' => $s->items->map(fn($i)=>[
                                    'id'             => $i->id, // optional
                                    'name'           => $i->name,
                                    'description'    => $i->description,
                                    'unit_id'        => $i->unit_id,
                                    'qty'            => $i->qty,
                                    'unit_price'     => $i->unit_price,
                                    'discount_type'  => $i->discount_type,
                                    'discount_value' => $i->discount_value,
                                    'order'          => $i->order,
                                ])
                            ])
                        ) !!}
                    </script>
                </div>
                <small class="text-muted">These will be saved with the variation on submit.</small>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('voyager.projects.variations.index',$project->id) }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
                @if(!empty($variation->total_amount))
                    <span class="fw-bold">Total: {{ number_format($variation->total_amount,2) }} {{ $variation->currency ?? 'AED' }}</span>
                @endif
            </div>
        </form>

    </div>
@endsection

@section('js_scripts')
    {{-- TinyMCE (same as quotation) --}}
    <script src="https://cdn.tiny.cloud/1/bh4l7c3n79u8x3a8qb76u4xv873vqdr0tw1gvtxwoke5v7nr/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>

    <script>
        // ---------- TinyMCE (quotation-like behavior) ----------
        let editorCounter = 0;

        function ensureEditor(textarea) {
            if (!textarea.id) textarea.id = 'tinymce-' + (++editorCounter);
            if (tinymce.get(textarea.id)) return;
            tinymce.init({
                target: textarea,
                menubar: false,
                plugins: 'lists link table code directionality',
                toolbar: 'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright | link | code',
                directionality: 'ltr',
                height: 200,
                branding: false,
                promotion: false
            });
        }

        function initEditors(root = document) {
            root.querySelectorAll('textarea.tinymce-editor').forEach(ensureEditor);
        }

        document.addEventListener('DOMContentLoaded', () => setTimeout(initEditors, 300));

        // ---------- Sections & Items (same look & table structure as quotation) ----------
        (function () {
            const units = @json($units->map(fn($u)=>['id'=>$u->id,'name'=>$u->name]));
            const container = document.getElementById('sectionsContainer');

            function itemRow(secIndex, itemIndex, it = {}) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${it.name || ''}</strong>
                        <div class="text-muted small">${it.description ? `${escapeHtml(stripTags(it.description)).slice(0, 80)}…` : ''}</div>
                        ${it.unit_id || it.qty ? `<div class="small text-muted">
                          ${it.unit_id ? `Unit: ${findUnitName(it.unit_id)}` : ''}
                          ${it.qty ? ` · Qty: ${fmtQty(it.qty)}` : ''}
                        </div>` : ''}
                    </td>
                    <td>${fmtMoney(it.unit_price ?? 0)}</td>
                    <td class="text-muted">Auto</td>
                    <td width="180">
                        <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse"
                                data-bs-target="#it-${secIndex}-${itemIndex}" onclick="initEditors()">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" data-act="remove-item">Delete</button>
                    </td>
                `;

                // Editor row (collapsible) same style as quotation
                const editTr = document.createElement('tr');
                editTr.className = 'collapse';
                editTr.id = `it-${secIndex}-${itemIndex}`;
                editTr.innerHTML = `
                  <td colspan="4">
                    <div class="border rounded p-2">
                      <div class="row g-2">
                        <div class="col-md-4">
                          <input class="form-control" name="sections[${secIndex}][items][${itemIndex}][name]" value="${it.name || ''}" placeholder="Item name" required>
                        </div>
                        <div class="col-md-2">
                          <input type="number" step="0.001" class="form-control" name="sections[${secIndex}][items][${itemIndex}][qty]" value="${it.qty ?? 1}" placeholder="Qty">
                        </div>
                        <div class="col-md-2">
                          <input type="number" step="0.01" class="form-control" name="sections[${secIndex}][items][${itemIndex}][unit_price]" value="${it.unit_price ?? 0}" placeholder="Price">
                        </div>
                        <div class="col-md-2">
                          <select class="form-select" name="sections[${secIndex}][items][${itemIndex}][unit_id]">
                            <option value="">— Unit —</option>
                            ${units.map(u => `<option value="${u.id}" ${String(it.unit_id || '') === String(u.id) ? 'selected' : ''}>${u.name}</option>`).join('')}
                          </select>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label small mb-1">Notes</label>
                          <textarea class="form-control tinymce-editor" name="sections[${secIndex}][items][${itemIndex}][description]">${it.description || ''}</textarea>
                        </div>
                        <div class="col-md-2">
                          <select class="form-select" name="sections[${secIndex}][items][${itemIndex}][discount_type]">
                            ${['none', 'percent', 'fixed'].map(t => `<option value="${t}" ${it.discount_type === t ? 'selected' : ''}>${t}</option>`).join('')}
                          </select>
                        </div>
                        <div class="col-md-2">
                          <input type="number" step="0.01" class="form-control" name="sections[${secIndex}][items][${itemIndex}][discount_value]" value="${it.discount_value ?? 0}" placeholder="Discount">
                        </div>
                        <input type="hidden" name="sections[${secIndex}][items][${itemIndex}][order]" value="${it.order || itemIndex + 1}">
                        ${it.id ? `<input type="hidden" name="sections[${secIndex}][items][${itemIndex}][id]" value="${it.id}">` : ''}
                      </div>
                    </div>
                  </td>
                `;

                // init editors in the new row when opened
                setTimeout(() => initEditors(editTr), 0);

                const frag = document.createDocumentFragment();
                frag.appendChild(tr);
                frag.appendChild(editTr);

                // delete handler
                tr.querySelector('[data-act="remove-item"]').addEventListener('click', () => {
                    tr.remove();
                    editTr.remove();
                    reindex();
                });

                return frag;
            }

            function sectionCard(secIndex, data = {title: '', description: '', order: secIndex + 1, items: []}) {
                const wrap = document.createElement('div');
                wrap.className = 'card p-3 mb-3';
                wrap.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0">${escapeHtml(data.title || 'Untitled')}</h5>
                            ${data.description ? `<div class="text-muted small">${escapeHtml(stripTags(data.description)).slice(0, 120)}…</div>` : ''}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#sec-edit-${secIndex}" onclick="initEditors()">Edit Section</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-act="remove-section">Delete</button>
                        </div>
                    </div>

                    <div id="sec-edit-${secIndex}" class="collapse mt-2">
                        <div class="border rounded p-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input name="sections[${secIndex}][title]" value="${data.title || ''}" class="form-control" placeholder="Section title">
                                </div>
                                <div class="col-md-7">
                                    <textarea name="sections[${secIndex}][description]" class="form-control tinymce-editor" placeholder="Short description (optional)">${data.description || ''}</textarea>
                                </div>
                                <div class="col-md-1">
                                    <input type="hidden" name="sections[${secIndex}][order]" value="${data.order || secIndex + 1}">
                                    ${data.id ? `<input type="hidden" name="sections[${secIndex}][id]" value="${data.id}">` : ''}
                                    <button class="btn btn-primary w-100" type="button" data-act="save-section" disabled>Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm align-middle">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Unit/Lump Price</th>
                                <th>Total</th>
                                <th width="180">Actions</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="border rounded p-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input class="form-control" placeholder="New item name" data-ref="new-name">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.001" class="form-control" placeholder="Qty" value="1" data-ref="new-qty">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" class="form-control" placeholder="Price" data-ref="new-price">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" data-ref="new-unit">
                                        <option value="">— Unit —</option>
                                        ${units.map(u => `<option value="${u.id}">${u.name}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-success w-100" type="button" data-act="add-item">+ Add Item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const tbody = wrap.querySelector('tbody');
                (data.items || []).forEach((it, idx) => tbody.appendChild(itemRow(secIndex, idx, it)));

                wrap.addEventListener('click', (e) => {
                    // add item
                    if (e.target.matches('[data-act="add-item"]')) {
                        const name = wrap.querySelector('[data-ref="new-name"]').value.trim();
                        if (!name) return;
                        const qty = parseFloat(wrap.querySelector('[data-ref="new-qty"]').value || '1');
                        const price = parseFloat(wrap.querySelector('[data-ref="new-price"]').value || '0');
                        const unitId = wrap.querySelector('[data-ref="new-unit"]').value || '';
                        const idx = tbody.querySelectorAll('tr').length / 2; // two rows per item
                        tbody.appendChild(itemRow(secIndex, idx, {
                            name, qty, unit_price: price, unit_id: unitId, discount_type: 'none', discount_value: 0
                        }));
                        // reset quick add inputs
                        wrap.querySelector('[data-ref="new-name"]').value = '';
                        wrap.querySelector('[data-ref="new-price"]').value = '';
                        wrap.querySelector('[data-ref="new-qty"]').value = '1';
                        wrap.querySelector('[data-ref="new-unit"]').value = '';
                        reindex();
                    }
                    // remove section
                    if (e.target.matches('[data-act="remove-section"]')) {
                        wrap.remove();
                        reindex();
                    }
                });

                // ensure any editors inside section edit collapse are initialized
                setTimeout(() => initEditors(wrap), 0);
                return wrap;
            }

            function reindex() {
                // Reindex sections
                [...container.children].forEach((card, sIdx) => {
                    // Section fields
                    card.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/sections\[\d+]/, `sections[${sIdx}]`);
                    });
                    const secOrder = card.querySelector(`input[name="sections[${sIdx}][order]"]`);
                    if (secOrder) secOrder.value = sIdx + 1;

                    // Items (two rows per item: display + editor)
                    const rows = card.querySelectorAll('tbody tr');
                    let logicalIndex = -1;
                    rows.forEach((row, idx) => {
                        if (idx % 2 === 0) logicalIndex++; // only count display rows
                        row.querySelectorAll('[name]').forEach(el => {
                            el.name = el.name.replace(/sections\[\d+]\[items]\[\d+]/, `sections[${sIdx}][items][${logicalIndex}]`);
                        });
                        const hiddenOrder = row.querySelector(`input[name="sections[${sIdx}][items][${logicalIndex}][order]"]`);
                        if (hiddenOrder) hiddenOrder.value = logicalIndex + 1;
                        // update collapse id/target to keep them unique after reindex
                        if (row.id && row.id.startsWith('it-')) row.id = `it-${sIdx}-${logicalIndex}`;
                        const btn = card.querySelector(`[data-bs-target="#it-${sIdx}-${logicalIndex}"]`);
                        if (btn) btn.setAttribute('data-bs-target', `#it-${sIdx}-${logicalIndex}`);
                    });
                });
            }

            // Utilities
            function fmtMoney(v) {
                return Number(v || 0).toFixed(2);
            }

            function fmtQty(v) {
                return (parseFloat(v || 1)).toString();
            }

            function findUnitName(id) {
                const u = units.find(x => String(x.id) === String(id));
                return u ? u.name : '';
            }

            function stripTags(html) {
                return (html || '').replace(/<[^>]*>/g, '');
            }

            function escapeHtml(str) {
                return (str || '').replace(/[&<>"']/g, s => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[s]));
            }

            // Add section
            document.getElementById('btnAddSection')?.addEventListener('click', () => {
                container.appendChild(sectionCard(container.children.length));
            });

            // Prefill or start with one section
            const prefill = document.getElementById('prefill-sections');
            try {
                const parsed = JSON.parse(prefill.textContent || '[]');
                if (parsed.length) parsed.forEach((sec, i) => container.appendChild(sectionCard(i, sec)));
                else container.appendChild(sectionCard(0));
            } catch {
                container.appendChild(sectionCard(0));
            }
        })();
    </script>
@endsection
