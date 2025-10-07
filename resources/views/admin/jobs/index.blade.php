@extends('admin.main')

@section('css_sheets')
    {{-- DataTables (Bootstrap 5) --}}
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.css') }}"/>

    <style>
        /* Match old "label" look used in actions/status */
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

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">الوظائف</h4>
            <a href="{{ route('voyager.jobs.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> إضافة وظيفة
            </a>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-muted">إجمالي الوظائف</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['active'] ?? 0 }}</div>
                        <div class="text-success">النشطة</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="display-6">{{ $stats['inactive'] ?? 0 }}</div>
                        <div class="text-danger">غير النشطة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <table id="jobs-table" class="table table-hover w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الوصف</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="no-sort" style="width:130px;">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('js_scripts')
    {{-- jQuery should already be included in your admin layout --}}
    <script src="{{ asset('admin-assets/lib/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin-assets/lib/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function () {
            const LOAD_URL = "{{ route('voyager.jobs.load') }}";

            const table = $('#jobs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {url: LOAD_URL},
                order: [[4, 'desc']],
                language: {
                    url: "{{ asset('admin-assets/i18n/ar.json') }}" // optional: your Arabic DT file
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description', orderable: false},
                    {data: 'is_active', name: 'is_active', searchable: false, orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', searchable: false, orderable: false},
                ]
            });

            // Toggle Active
            $(document).on('click', '.js-toggle', function () {
                const url = $(this).data('url');
                $.post(url, {_token: $('meta[name="csrf-token"]').attr('content')}, function () {
                    table.ajax.reload(null, false);
                });
            });

            // Delete
            $(document).on('click', '.delete-record', function (e) {
                e.preventDefault();

                const url = $(this).data('url');
                if (!confirm('هل أنت متأكد من الحذف؟')) return;

                $.ajax({
                    url: url,
                    type: 'POST', // ← use POST
                    data: {
                        _method: 'DELETE', // ← spoof DELETE for Laravel
                        _token: $('meta[name="csrf-token"]').attr('content') // ← CSRF
                    },
                    success: function () {
                        $('#jobs-table').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : ('HTTP ' + xhr.status);
                        alert('فشل الحذف: ' + msg);
                    }
                });
            });

        });
    </script>
@endsection
