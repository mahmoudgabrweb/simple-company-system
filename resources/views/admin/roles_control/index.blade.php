@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        <span>Roles Matrix</span>
                        <a href="{{ route('voyager.roles.index-new') }}" class="btn btn-secondary">Back to Roles</a>
                    </h5>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('voyager.role_assignables.store') }}" class="mb-3">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="roles-matrix">
                                    <thead>
                                    <tr>
                                        <th style="min-width:240px">Role \ Can Assign</th>
                                        @foreach ($roles as $col)
                                            <th class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span>{{ $col->display_name ?? $col->name }}</span>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary mt-1 col-select"
                                                            data-col="{{ $col->id }}">Select
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary mt-1 col-unselect"
                                                            data-col="{{ $col->id }}">Unselect
                                                    </button>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($roles as $row)
                                        @php $checked = $rows[$row->id] ?? []; @endphp
                                        <tr data-row="{{ $row->id }}">
                                            <th>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ $row->display_name ?? $row->name }}</span>
                                                    <div class="d-flex gap-1">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary row-select"
                                                                data-row="{{ $row->id }}">Select Row
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary row-unselect"
                                                                data-row="{{ $row->id }}">Unselect
                                                        </button>
                                                    </div>
                                                </div>
                                            </th>
                                            @foreach ($roles as $col)
                                                <td class="text-center">
                                                    <input type="checkbox" class="form-check-input matrix-box"
                                                           name="assignable[{{ $row->id }}][]"
                                                           value="{{ $col->id }}"
                                                            {{ in_array($col->id, $checked) ? 'checked' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>

                        <p class="text-muted">
                            * This matrix defines which roles each role is allowed to assign when creating/editing
                            users.
                            The <strong>admin</strong> role is implicitly allowed to assign all roles.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_scripts')
    <script>
        // Row bulk
        document.querySelectorAll('.row-select').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.dataset.row;
                document.querySelectorAll('tr[data-row="' + row + '"] .matrix-box').forEach(ch => ch.checked = true);
            });
        });
        document.querySelectorAll('.row-unselect').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.dataset.row;
                document.querySelectorAll('tr[data-row="' + row + '"] .matrix-box').forEach(ch => ch.checked = false);
            });
        });

        // Column bulk
        document.querySelectorAll('.col-select').forEach(btn => {
            btn.addEventListener('click', function () {
                const col = this.dataset.col;
                const index = Array.from(this.closest('table').querySelectorAll('thead th'))
                    .findIndex(th => th.querySelector('[data-col="' + col + '"]'));
                document.querySelectorAll('#roles-matrix tbody tr').forEach(tr => {
                    const cell = tr.children[index];
                    const input = cell.querySelector('input[type="checkbox"]');
                    if (input) input.checked = true;
                });
            });
        });
        document.querySelectorAll('.col-unselect').forEach(btn => {
            btn.addEventListener('click', function () {
                const col = this.dataset.col;
                const index = Array.from(this.closest('table').querySelectorAll('thead th'))
                    .findIndex(th => th.querySelector('[data-col="' + col + '"]'));
                document.querySelectorAll('#roles-matrix tbody tr').forEach(tr => {
                    const cell = tr.children[index];
                    const input = cell.querySelector('input[type="checkbox"]');
                    if (input) input.checked = false;
                });
            });
        });
    </script>
@stop
