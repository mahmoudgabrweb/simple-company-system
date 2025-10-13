@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Quotation #{{ $quotation->id }}</h4>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">Back</a>
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

        {{-- Header --}}
        <form action="{{ route('voyager.quotations.update',$quotation->id) }}" method="post" class="card p-3 mb-3">
            @csrf @method('PUT')
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <input class="form-control" value="{{ $quotation->project->name ?? '' }}" disabled>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Quotation No.</label>
                    <input name="quotation_number" class="form-control" value="{{ $quotation->quotation_number }}">
                </div>

                <div class="col-md-1">
                    <label class="form-label">Version</label>
                    <input type="number" min="1" name="version" value="{{ $quotation->version }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft','sent','waiting_client_response','approved','rejected','in_progress','completed'] as $st)
                            <option value="{{ $st }}" @selected($quotation->status===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Active?</label>
                    <select name="is_active" class="form-select">
                        <option value="0" @selected(!$quotation->is_active)>No</option>
                        <option value="1" @selected($quotation->is_active)>Yes</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until"
                           value="{{ $quotation->valid_until?->format('Y-m-d') }}"
                           class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control tinymce">{{ $quotation->notes }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>

                <form class="d-inline" action="{{ route('admin.quotations.resend',$quotation->id) }}" method="post"
                      onsubmit="return confirm('Send (or re-send) this quotation to the client?');">
                    @csrf
                    <button class="btn btn-outline-primary">Send / Resend to Client</button>
                </form>

                @if($quotation->pdf_path)
                    <a class="btn btn-outline-secondary" target="_blank"
                       href="{{ asset('storage/'.$quotation->pdf_path) }}">
                        View PDF
                    </a>
                @endif

                <span class="float-end fw-bold">Total: {{ number_format($quotation->total_amount,2) }}</span>
            </div>
        </form>

        {{-- Sections + Items --}}
        <div id="sections-sortable">
            @foreach($quotation->sections as $section)
                <div class="card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0">{{ $section->letter_code ? $section->letter_code.'. ' : '' }}{{ $section->title }}</h5>
                            @if($section->description)
                                <div class="text-muted small">{!! $section->description !!}</div>
                            @endif
                            @if($section->is_static)
                                <span class="badge bg-info">Static</span>
                            @endif
                        </div>
                        <div>
                            @if(!$section->is_static)
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                        data-bs-target="#sec-edit-{{ $section->id }}">Edit Section
                                </button>
                                <form class="d-inline"
                                      action="{{ route('admin.quotations.sections.delete', $section->id) }}"
                                      method="post"
                                      onsubmit="return confirm('Delete this section?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- edit section --}}
                    @if(!$section->is_static)
                        <div id="sec-edit-{{ $section->id }}" class="collapse mt-2">
                            <form action="{{ route('admin.quotations.sections.update', $section->id) }}" method="post"
                                  class="border rounded p-2">
                                @csrf @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input name="title" value="{{ $section->title }}" class="form-control"
                                               placeholder="Section title">
                                    </div>
                                    <div class="col-md-7">
                                        <input name="description" value="{{ $section->description }}"
                                               class="form-control tinymce"
                                               placeholder="Short description (optional)">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary w-100">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- items table --}}
                    <div class="table-responsive mt-3">
                        <table class="table table-sm align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Unit/Lump Price</th>
                                <th>Total</th>
                                <th width="180">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($section->allItems as $it)
                                <tr>
                                    <td>{{ $it->id }}</td>
                                    <td>
                                        <strong>{{ $it->title }}</strong>
                                        @if($it->description)
                                            <div class="text-muted small">{!! Str::limit(strip_tags($it->description),80) !!}</div>
                                        @endif
                                        @if($it->parent_item_id)
                                            <div class="small text-muted">Child of item #{{ $it->parent_item_id }}</div>
                                        @endif
                                        @if($it->is_excluded)
                                            <span class="badge bg-warning text-dark">Excluded</span>
                                        @endif
                                    </td>
                                    <td>{{ $it->quantity ?? '—' }}</td>
                                    <td>{{ $it->unit?->code ?? '—' }}</td>
                                    <td>{{ number_format($it->unit_price ?? 0,2) }}</td>
                                    <td>{{ number_format($it->total_price,2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="collapse"
                                                data-bs-target="#it-{{ $it->id }}">Edit
                                        </button>
                                        <form class="d-inline"
                                              action="{{ route('admin.quotations.items.delete',$it->id) }}"
                                              method="post"
                                              onsubmit="return confirm('Delete this item?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="it-{{ $it->id }}">
                                    <td colspan="7">
                                        <form action="{{ route('admin.quotations.items.update', $it->id) }}"
                                              method="post"
                                              class="border rounded p-2">
                                            @csrf @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input name="title" value="{{ $it->title }}" class="form-control"
                                                           required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input name="description"
                                                           value="{{ old('description',$it->description) }}"
                                                           class="form-control tinymce"
                                                           placeholder="Description (optional)">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" step="0.001" name="quantity"
                                                           value="{{ $it->quantity }}"
                                                           class="form-control" placeholder="Qty">
                                                </div>
                                                <div class="col-md-1">
                                                    <select name="unit_id" class="form-select">
                                                        <option value="">—</option>
                                                        @foreach($units as $u)
                                                            <option value="{{ $u->id }}" @selected($it->unit_id==$u->id)>{{ $u->code }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" step="0.01" name="unit_price"
                                                           value="{{ $it->unit_price }}"
                                                           class="form-control" placeholder="Price">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small mb-1">Notes</label>
                                                    <textarea name="description" class="form-control tinymce"
                                                              rows="3">{{ $it->description }}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="is_excluded" class="form-select">
                                                        <option value="0" @selected(!$it->is_excluded)>Included</option>
                                                        <option value="1" @selected($it->is_excluded)>Excluded</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button class="btn btn-primary w-100">Save Item</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No items.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- add item --}}
                    <form action="{{ route('admin.quotations.items.add', $section->id) }}" method="post"
                          class="border rounded p-2">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input name="title" class="form-control" placeholder="New item" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.001" name="quantity" class="form-control"
                                       placeholder="Qty">
                            </div>
                            <div class="col-md-1">
                                <select name="unit_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" name="unit_price" class="form-control"
                                       placeholder="Price">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success w-100">+ Add Item</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        {{-- add dynamic section --}}
        <div class="card p-3">
            <form action="{{ route('admin.quotations.sections.add',$quotation->id) }}" method="post">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <input name="title" class="form-control" placeholder="Section title" required>
                    </div>
                    <div class="col-md-5">
                        <input name="description" class="form-control tinymce"
                               placeholder="Short description (optional)">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">+ New Section</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: 'textarea.tinymce, .tinymce',
                menubar: false,
                plugins: 'lists link table code directionality',
                toolbar: 'undo redo | styles | bold italic underline | bullist numlist | alignleft aligncenter alignright | link table | ltr rtl | code',
                directionality: 'ltr', // switched to LTR
                height: 220,
                convert_urls: false,
                relative_urls: false,
                entity_encoding: 'raw',
                branding: false,
            });
        });
    </script>
@endpush
