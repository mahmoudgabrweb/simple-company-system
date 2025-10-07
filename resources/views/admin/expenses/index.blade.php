@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.css') }}"/>
    <style>.table thead th {
            white-space: nowrap
        }</style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">المصروفات</h4>
            <a href="{{ route('voyager.expenses.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> إضافة مصروف
            </a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-muted">عدد السجلات</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ number_format($stats['amount'] ?? 0, 2) }}</div>
                        <div class="text-muted">إجمالي المبالغ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">تاريخ المصروف</label>
                        <input type="date" id="filter-spent-at" class="form-control" value="">
                    </div>
                    <div class="col-md-3">
                        <button id="btn-apply-filter" class="btn btn-primary">تطبيق</button>
                        <button id="btn-clear-filter" class="btn btn-outline-secondary">مسح</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="expenses-table" class="table table-hover w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>المبلغ</th>
                        <th>أُنشئ بواسطة</th>
                        <th>تاريخ المصروف</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="no-sort" style="width:160px;">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('admin-assets/lib/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function () {
            const table = $('#expenses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('voyager.expenses.load') }}",
                    data: function (d) {
                        d.spent_at = $('#filter-spent-at').val() || '';
                    }
                },
                order: [[5, 'desc']], // adjust index if you inserted a column
                language: {url: "{{ asset('admin-assets/i18n/ar.json') }}"},
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'title', name: 'title'},
                    {data: 'type', name: 'type', orderable: false, searchable: false},
                    {data: 'amount', name: 'amount'},
                    {data: 'by', name: 'by', orderable: false, searchable: false},
                    {data: 'spent_at', name: 'spent_at'},       // NEW
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', orderable: false, searchable: false},
                ]
            });

            $('#btn-apply-filter').on('click', function () {
                table.ajax.reload();
            });
            $('#btn-clear-filter').on('click', function () {
                $('#filter-spent-at').val('');
                table.ajax.reload();
            });

            // Delete (POST + _method=DELETE)
            $(document).on('click', '.delete-record', function (e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (!confirm('هل أنت متأكد من الحذف؟')) return;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content')},
                    success: function () {
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        alert('فشل الحذف: ' + (xhr.responseJSON?.message || 'HTTP ' + xhr.status));
                    }
                });
            });
        });
    </script>
@endsection
