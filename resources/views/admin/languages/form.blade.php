@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-language"></i></div>
            <div>
                <h4 class="m-page-title">{{ $language ? 'Edit' : 'Add' }} Language</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.languages.index') }}">Languages</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $language ? 'Edit' : 'Add' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ $language ? route('admin.languages.update', $language) : route('admin.languages.store') }}" method="POST">
                @csrf
                @if($language) @method('PUT') @endif
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $language?->name) }}" required placeholder="e.g. English">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $language?->code) }}" required placeholder="e.g. en">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Native Name</label>
                        <input type="text" name="native_name" class="form-control" value="{{ old('native_name', $language?->native_name) }}" placeholder="e.g. English">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Direction <span class="text-danger">*</span></label>
                        <select name="direction" class="form-select" required>
                            <option value="ltr" {{ old('direction', $language?->direction) == 'ltr' ? 'selected' : '' }}>LTR</option>
                            <option value="rtl" {{ old('direction', $language?->direction) == 'rtl' ? 'selected' : '' }}>RTL</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default Language</label>
                        <select name="is_default" class="form-select">
                            <option value="0" {{ old('is_default', $language?->is_default) ? '' : 'selected' }}>No</option>
                            <option value="1" {{ old('is_default', $language?->is_default) ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $language?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $language?->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ $language ? 'Update' : 'Create' }} Language</button>
            </form>
        </div>
    </div>
</div>
@endsection
