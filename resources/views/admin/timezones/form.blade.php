@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $timezone ? 'Edit' : 'Add' }} Timezone</h5>
        </div>
        <div class="card-body">
            <form action="{{ $timezone ? route('admin.timezones.update', $timezone) : route('admin.timezones.store') }}" method="POST">
                @csrf
                @if($timezone) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $timezone->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label', $timezone->label ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Offset</label>
                        <input type="text" name="offset" class="form-control" value="{{ old('offset', $timezone->offset ?? '') }}" placeholder="+05:30">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Offset Minutes</label>
                        <input type="number" name="offset_minutes" class="form-control" value="{{ old('offset_minutes', $timezone->offset_minutes ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ ($timezone->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ ($timezone->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.timezones.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
