@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-check-shield"></i></div>
            <div>
                <h4 class="m-page-title">Edit Role</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Edit</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="m-section-divider">Role Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $role->description) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-4">Module Permissions</div>
                <div class="mb-3">
                    @error('permissions') <div class="text-danger mb-2">{{ $message }}</div> @enderror
                    <div class="row">
                        @foreach($grouped as $module => $perms)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border shadow-none">
                                <div class="card-header border-bottom py-2 bg-label-primary d-flex align-items-center">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input module-check" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                        <label class="form-check-label fw-bold text-capitalize" for="module_{{ $module }}">{{ str_replace('_', ' ', $module) }}</label>
                                    </div>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-2">
                                        @foreach($perms as $perm)
                                        <div class="col-12 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" data-module="{{ $module }}" {{ in_array($perm->id, old('permissions', $assignedPermissions)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="m-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Update Role</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.module-check').on('change', function() {
        var module = $(this).data('module');
        $('.perm-check[data-module="' + module + '"]').prop('checked', $(this).is(':checked'));
    });

    $('.perm-check').on('change', function() {
        var module = $(this).data('module');
        var total = $('.perm-check[data-module="' + module + '"]').length;
        var checked = $('.perm-check[data-module="' + module + '"]:checked').length;
        $('#module_' + module).prop('checked', total === checked);
    });

    $('.perm-check').each(function() {
        var module = $(this).data('module');
        var total = $('.perm-check[data-module="' + module + '"]').length;
        var checked = $('.perm-check[data-module="' + module + '"]:checked').length;
        $('#module_' + module).prop('checked', total === checked);
    });
});
</script>
@endsection
