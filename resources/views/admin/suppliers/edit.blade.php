@extends('admin.main')

@section('css_sheets')
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/select2-bootstrap-5-theme.min.css') }}">
    <style>.select2-container {
            width: 100% !important
        }</style>
@endsection

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Supplier #{{ $supplier->id }}</h4>
            <a href="{{ route('voyager.suppliers.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.suppliers.update', $supplier->id) }}" method="POST"
              enctype="multipart/form-data" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                {{-- Supplier Info --}}
                <div class="col-md-6">
                    <label class="form-label">Supplier Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$supplier->name) }}"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$supplier->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone',$supplier->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <select name="city_id" class="form-select js-select2-city">
                        <option value="">Select city</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->id }}" @selected(old('city_id',$supplier->city_id)==$c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ old('address',$supplier->address) }}">
                </div>

                {{-- Contact Person --}}
                <div class="col-md-6">
                    <label class="form-label">Contact Person Name *</label>
                    <input type="text" name="contact_person_name" class="form-control"
                           value="{{ old('contact_person_name',$supplier->contact_person_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Person Phone *</label>
                    <input type="text" name="contact_person_phone" class="form-control"
                           value="{{ old('contact_person_phone',$supplier->contact_person_phone) }}" required>
                </div>

                {{-- Bank Info --}}
                <div class="col-md-4">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control"
                           value="{{ old('bank_name',$supplier->bank_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">IBAN</label>
                    <input type="text" name="iban" class="form-control" value="{{ old('iban',$supplier->iban) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SWIFT</label>
                    <input type="text" name="swift" class="form-control" value="{{ old('swift',$supplier->swift) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" @selected(old('status',$supplier->status)=='active')>Active</option>
                        <option value="inactive" @selected(old('status',$supplier->status)=='inactive')>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                    @if($supplier->attachment_path)
                        <div class="mt-1">
                            <a href="{{ route('voyager.suppliers.download',$supplier->id) }}"
                               class="small text-primary">
                                Current file
                            </a>
                        </div>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes',$supplier->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-assets/lib/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('.js-select2-city').select2({theme: 'bootstrap-5', placeholder: 'Select city', allowClear: true});
        });
    </script>
@endpush
