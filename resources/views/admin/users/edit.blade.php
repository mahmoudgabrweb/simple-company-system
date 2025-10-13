@extends('admin.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header">Edit User</h5>
                    <div class="card-body">
                        <form method="POST" action="{{ route('voyager.users.update', $user->id) }}" class="row g-4">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password (leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control" required>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}"
                                                {{ (string) old('role_id', $currentRoleId) === (string) $r->id ? 'selected' : '' }}>
                                            {{ $r->display_name ?? $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (array_key_exists('is_active', $user->getAttributes()))
                                <div class="col-md-6">
                                    <label class="form-label d-block">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('voyager.users.index-new') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
