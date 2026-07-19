@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Service</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.services.update', $service) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="service_category_id" class="form-select @error('service_category_id') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('service_category_id', $service->service_category_id)==$c->id ? 'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('service_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Labor Charge</label>
                    <input type="number" step="0.01" name="labor_charge" class="form-control @error('labor_charge') is-invalid @enderror" value="{{ old('labor_charge', $service->labor_charge) }}">
                    @error('labor_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $service->description) }}</textarea>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
