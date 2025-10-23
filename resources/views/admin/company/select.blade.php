@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}">
    <style>
        .select2-container {
            width: 100% !important
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #d9dee3);
            border-radius: .375rem;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .select2-selection__arrow {
            height: 38px !important;
            right: .5rem !important
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        <div class="clearfix container-fluid">
            <h2 class="page-title">Select Company</h2>
            <form method="POST" action="{{ route('admin.company.set') }}">
                @csrf
                <div class="form-group">
                    <label>Company</label>
                    <select name="company_id" class="form-control" required>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary mt-3">Proceed</button>
            </form>
        </div>
    </div>
@endsection
