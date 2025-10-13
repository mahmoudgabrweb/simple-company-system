{{-- resources/views/admin/variations/create.blade.php --}}
@extends('admin.main')
@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Variation — Project: {{ $project->name }}</h4>
            <a href="{{ route('voyager.projects.variations.index',$project->id) }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="alert alert-info">
            Active Quotation: #{{ $activeQuotation->id }} (Status: {{ ucfirst($activeQuotation->status) }})
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.projects.variations.store',$project->id) }}" method="post" class="card card-body">
            @include('admin.variations._form')
        </form>
    </div>
@endsection
