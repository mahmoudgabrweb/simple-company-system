@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.css') }}"/>
    <style>
        .label {
            display: inline-block;
            padding: .35rem .6rem;
            font-size: .75rem;
            border-radius: .25rem;
        }

        .label-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .label-danger {
            background: #f8d7da;
            color: #842029;
        }

        .table thead th {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">الموظفون</h4>
            <a href="{{ route('voyager.employees.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> إضافة موظف
            </a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-muted">إجمالي الموظفين</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['active'] ?? 0 }}</div>
                        <div class="text-success">النشطون</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['inactive'] ?? 0 }}</div>
                        <div class="text-danger">غير النشطين</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="employees-table" class="table table-hover w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الوظيفة</th>
                        <th>الهاتف</th>
                        <th>البريد</th>
                        <th>الحالة</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الراتب</th>
                        <th class="no-sort" style="width:170px;">إجراءات</th>
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
            const table = $('#employees-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {url: "{{ route('voyager.employees.load') }}"},
                order: [[9, 'desc']],
                language: {url: "{{ asset('admin-assets/i18n/ar.json') }}"},
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'name', name: 'name'},
                    {data: 'job', name: 'job', orderable: false, searchable: false},
                    {data: 'phone', name: 'phone'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'start_at', name: 'start_at'},
                    {data: 'end_at', name: 'end_at'},
                    {data: 'salary', name: 'salary'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });

            // Toggle
            $(document).on('click', '.js-toggle', function () {
                $.post($(this).data('url'), {_token: $('meta[name="csrf-token"]').attr('content')}, function () {
                    table.ajax.reload(null, false);
                });
            });

            // Delete (POST + method spoof)
            $(document).on('click', '.delete-record', function (e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (!confirm('هل أنت متأكد من الحذف؟')) return;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
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
