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
            <h4 class="m-0">الرواتب</h4>
            <a href="{{ route('voyager.salaries.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> إضافة سجل
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

        <div class="card">
            <div class="card-body">
                <table id="salaries-table" class="table table-hover w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الموظف</th>
                        <th>الشهر</th>
                        <th>النوع</th>
                        <th>النوع المحاسبي</th>
                        <th>المبلغ</th>
                        <th>الأيام</th>
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
            const table = $('#salaries-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {url: "{{ route('voyager.salaries.load') }}"},
                order: [[8, 'desc']],
                language: {url: "{{ asset('admin-assets/i18n/ar.json') }}"},
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'title', name: 'title'},
                    {data: 'employee', name: 'employee', orderable: false, searchable: false},
                    {data: 'month', name: 'month'},
                    {data: 'type', name: 'type'},
                    {data: 'expense_type', name: 'expense_type', orderable: false, searchable: false},
                    {data: 'amount', name: 'amount'},
                    {data: 'days_count', name: 'days_count'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
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
