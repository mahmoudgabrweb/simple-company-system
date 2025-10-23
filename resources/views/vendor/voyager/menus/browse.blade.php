@extends('admin.main')

@section('css_sheets')
    <style>
        .table thead th { white-space: nowrap; }
        .actions-col { width: 1%; white-space: nowrap; }
    </style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">
                <i class="bx bx-list-ul me-1"></i>
                {{ $dataType->getTranslatedAttribute('display_name_plural') }}
            </h4>
            @can('add', app($dataType->model_name))
                <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> {{ __('voyager::generic.add_new') }}
                </a>
            @endcan
        </div>

        {{-- Voyager alerts (success/error) --}}
        @include('voyager::alerts')

        {{-- Optional: Info notice from Voyager menus partial (kept for compatibility) --}}
        @include('voyager::menus.partial.notice')

        {{-- Content Card --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        @foreach($dataType->browseRows as $row)
                            <th>{{ $row->display_name }}</th>
                        @endforeach
                        <th class="text-end actions-col">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($dataTypeContent as $data)
                        <tr>
                            @foreach($dataType->browseRows as $row)
                                @php
                                    $value = $data->{$row->field};
                                    $isImage = $row->type === 'image';
                                @endphp
                                <td>
                                    @if($isImage)
                                        @php
                                            $src = $value;
                                            if ($value && !str_starts_with($value, 'http://') && !str_starts_with($value, 'https://')) {
                                                $src = Voyager::image($value);
                                            }
                                        @endphp
                                        @if($src)
                                            <img src="{{ $src }}" alt="" style="width:100px;max-height:70px;object-fit:cover;border-radius:6px;">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @else
                                        {{ is_scalar($value) || is_null($value) ? ($value ?? '—') : json_encode($value) }}
                                    @endif
                                </td>
                            @endforeach

                            <td class="text-end">
                                @can('delete', $data)
                                    <form action="{{ route('voyager.'.$dataType->slug.'.destroy', $data->{$data->getKeyName()}) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('voyager::generic.are_you_sure') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bx bx-trash"></i> {{ __('voyager::generic.delete') }}
                                        </button>
                                    </form>
                                @endcan

                                @can('edit', $data)
                                    <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data->{$data->getKeyName()}) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-edit"></i> {{ __('voyager::generic.edit') }}
                                    </a>
                                @endcan

                                @can('edit', $data)
                                    @if(\Illuminate\Support\Facades\Route::has('voyager.'.$dataType->slug.'.builder'))
                                        <a href="{{ route('voyager.'.$dataType->slug.'.builder', $data->{$data->getKeyName()}) }}"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bx bx-list-ol"></i> {{ __('voyager::generic.builder') }}
                                        </a>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $dataType->browseRows->count() + 1 }}" class="text-center text-muted p-4">
                                {{ __('voyager::generic.no_results') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (works if $dataTypeContent is a paginator) --}}
            @if(method_exists($dataTypeContent, 'links'))
                <div class="card-body">
                    {{ $dataTypeContent->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
