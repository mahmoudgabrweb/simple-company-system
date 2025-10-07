@extends('voyager::master')

@section('content')
    <div class="page-content">
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
