@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.css') }}"/>
    <style>.table thead th {
            white-space: nowrap
        }

        .label {
            display: inline-block;
            padding: .35rem .6rem;
            font-size: .75rem;
            border-radius: .25rem
        }</style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">المشاريع</h4>
            <a href="{{ route('voyager.projects.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> إضافة مشروع
            </a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-muted">إجمالي المشاريع</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="projects-table" class="table table-hover w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>العميل</th>
                        <th>المدينة</th>
                        <th>العنوان</th>
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
            const table = $('#projects-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {url: "{{ route('voyager.projects.load') }}"},
                order: [[5, 'desc']],
                language: {url: "{{ asset('admin-assets/i18n/ar.json') }}"},
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'name', name: 'name'},
                    {data: 'client', name: 'client', orderable: false, searchable: false},
                    {data: 'city', name: 'city', orderable: false, searchable: false},
                    {data: 'address', name: 'address', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });

            // Delete (POST + method spoof)
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
