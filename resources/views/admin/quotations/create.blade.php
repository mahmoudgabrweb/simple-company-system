@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Create New Quotation</h4>
            <a href="{{ route('voyager.quotations.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('voyager.quotations.store') }}" method="post" class="card p-3">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-control"
                           value="{{ $currentCompany->name ?? 'Will be saved under the active company' }}" disabled>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">— Select a project —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Quotation No.</label>
                    <input name="quotation_number" class="form-control" value="{{ old('quotation_number') }}">
                </div>

                <div class="col-md-1">
                    <label class="form-label">Version</label>
                    <input type="number" min="1" name="version" class="form-control" value="{{ old('version', 1) }}">
                </div>

                <div class="col-md-1">
                    <label class="form-label">Active?</label>
                    <select name="is_active" class="form-select">
                        <option value="0" @selected(old('is_active')==='0')>No</option>
                        <option value="1" @selected(old('is_active')==='1')>Yes</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save & Create</button>
                <a class="btn btn-secondary" href="{{ route('voyager.quotations.index') }}">Back</a>
            </div>
        </form>
    </div>
@endsection
