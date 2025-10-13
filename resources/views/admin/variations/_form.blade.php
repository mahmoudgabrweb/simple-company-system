{{-- resources/views/admin/variations/_form.blade.php --}}
@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $variation->title ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        @php $statuses = ['draft','sent','accepted','rejected','expired','completed']; @endphp
        <select name="status" class="form-control">
            @foreach($statuses as $s)
                <option value="{{ $s }}" @selected(old('status', $variation->status ?? 'draft')==$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Currency</label>
        <input type="text" name="currency" maxlength="3" class="form-control"
               value="{{ old('currency', $variation->currency ?? 'AED') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Valid Until</label>
        <input type="date" name="valid_until" class="form-control"
               value="{{ old('valid_until', isset($variation)?$variation->valid_until?->format('Y-m-d'):'') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $variation->notes ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="m-0">Sections & Items</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddSection">Add Section</button>
        </div>

        <div id="sectionsContainer">
            {{-- JS will render cards; on edit we inject existing payload below --}}
            @isset($variation)
                <script id="prefill-sections" type="application/json">
                    {!! json_encode($variation->sections->map(fn($s)=>[
                        'title'=>$s->title,'order'=>$s->order,'items'=>$s->items->map(fn($i)=>[
                          'name'=>$i->name,'description'=>$i->description,'unit_id'=>$i->unit_id,
                          'qty'=>$i->qty,'unit_price'=>$i->unit_price,
                          'discount_type'=>$i->discount_type,'discount_value'=>$i->discount_value,'order'=>$i->order
                        ])
                    ])) !!}
                </script>
            @endisset
        </div>
        <small class="text-muted">No static blocks; only dynamic sections/items.</small>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('voyager.projects.variations.index',$project->id) }}" class="btn btn-secondary">Cancel</a>
</div>

@push('javascript')
    <script>
        (function () {
            const units = @json($units->map(fn($u)=>['id'=>$u->id,'name'=>$u->name]));
            const container = document.getElementById('sectionsContainer');

            function sectionCard(secIndex, data = {title: '', order: secIndex + 1, items: []}) {
                const wrap = document.createElement('div');
                wrap.className = 'card mb-3';
                wrap.innerHTML = `
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div class="w-100 me-2">
            <label class="form-label">Section title</label>
            <input class="form-control" name="sections[${secIndex}][title]" value="${data.title || ''}">
            <input type="hidden" name="sections[${secIndex}][order]" value="${data.order || secIndex + 1}">
          </div>
          <button type="button" class="btn btn-outline-danger btn-sm ms-2" data-act="remove-section">Remove</button>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Items</strong>
            <button type="button" class="btn btn-outline-primary btn-sm" data-act="add-item">Add Item</button>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead><tr>
                <th>Name</th><th>Unit</th><th>Qty</th><th>Unit Price</th><th>Discount</th><th>Subtotal</th><th></th>
              </tr></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>`;
                // add items
                const tbody = wrap.querySelector('tbody');
                (data.items || []).forEach((it, idx) => tbody.appendChild(itemRow(secIndex, idx, it)));
                wrap.addEventListener('click', (e) => {
                    if (e.target.matches('[data-act="add-item"]')) {
                        const idx = tbody.querySelectorAll('tr').length;
                        tbody.appendChild(itemRow(secIndex, idx, {}));
                    }
                    if (e.target.matches('[data-act="remove-item"]')) {
                        e.target.closest('tr').remove();
                    }
                    if (e.target.matches('[data-act="remove-section"]')) {
                        wrap.remove();
                        reindex();
                    }
                });
                return wrap;
            }

            function itemRow(secIndex, itemIndex, it) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
      <td><input class="form-control" name="sections[${secIndex}][items][${itemIndex}][name]" value="${it.name || ''}"></td>
      <td>
        <select class="form-control" name="sections[${secIndex}][items][${itemIndex}][unit_id]">
          <option value="">—</option>
          ${units.map(u => `<option value="${u.id}" ${String(it.unit_id || '') === String(u.id) ? 'selected' : ''}>${u.name}</option>`).join('')}
        </select>
      </td>
      <td><input type="number" step="0.001" class="form-control" name="sections[${secIndex}][items][${itemIndex}][qty]" value="${it.qty ?? 1}"></td>
      <td><input type="number" step="0.01" class="form-control" name="sections[${secIndex}][items][${itemIndex}][unit_price]" value="${it.unit_price ?? 0}"></td>
      <td style="min-width:180px">
        <select class="form-control mb-1" name="sections[${secIndex}][items][${itemIndex}][discount_type]">
          ${['none', 'percent', 'fixed'].map(t => `<option value="${t}" ${it.discount_type === t ? 'selected' : ''}>${t}</option>`).join('')}
        </select>
        <input type="number" step="0.01" class="form-control"
          name="sections[${secIndex}][items][${itemIndex}][discount_value]" value="${it.discount_value ?? 0}">
      </td>
      <td class="text-muted">Auto</td>
      <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm" data-act="remove-item">×</button></td>
      <input type="hidden" name="sections[${secIndex}][items][${itemIndex}][order]" value="${it.order || itemIndex + 1}">
    `;
                return tr;
            }

            function reindex() {
                [...container.children].forEach((card, sIdx) => {
                    // rename inputs to keep indexes continuous after delete
                    card.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/sections\[\d+\]/, `sections[${sIdx}]`);
                        if (/\[items]\[\d+]/.test(el.name)) {
                            // fix items indexing inside each section
                        }
                    });
                });
            }

            document.getElementById('btnAddSection')?.addEventListener('click', () => {
                container.appendChild(sectionCard(container.children.length));
            });

            // Prefill for edit
            const prefill = document.getElementById('prefill-sections');
            if (prefill) {
                const parsed = JSON.parse(prefill.textContent || '[]');
                parsed.forEach((sec, i) => container.appendChild(sectionCard(i, sec)));
            } else {
                // create one section by default on create
                container.appendChild(sectionCard(0));
            }
        })();
    </script>
@endpush
