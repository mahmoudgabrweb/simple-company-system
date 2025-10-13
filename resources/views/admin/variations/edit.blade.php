{{-- resources/views/admin/variations/edit.blade.php --}}
@extends('admin.main')
@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Variation #{{ $variation->id }} — Project: {{ $project->name }}</h4>
            <a href="{{ route('voyager.projects.variations.index',$project->id) }}" class="btn btn-secondary">Back</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.projects.variations.update',[$project->id,$variation->id]) }}" method="post"
              class="card card-body">
            @method('PUT')
            @include('admin.variations._form', ['variation'=>$variation])
        </form>
    </div>
@endsection
