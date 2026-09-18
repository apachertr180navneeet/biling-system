@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-barcode"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($numberSeries) ? 'Edit Number Series' : 'Create Number Series' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.masters.number-series.index') }}">Number Series</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($numberSeries) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.masters.number-series.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($numberSeries) ? route('admin.masters.number-series.update', $numberSeries) : route('admin.masters.number-series.store') }}" method="POST">
                @csrf
                @if(isset($numberSeries)) @method('PUT') @endif
                
                <div class="m-section-divider">Series Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control" value="{{ old('module', $numberSeries?->module) }}" placeholder="e.g. invoice, quotation" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $numberSeries?->description) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Formatting</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prefix</label>
                        <input type="text" name="prefix" class="form-control" value="{{ old('prefix', $numberSeries?->prefix) }}" placeholder="e.g. INV-">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $numberSeries?->suffix) }}" placeholder="e.g. -ORD">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Padding (zeros) <span class="text-danger">*</span></label>
                        <input type="number" name="padding" class="form-control" value="{{ old('padding', $numberSeries?->padding ?? 6) }}" min="1" max="10" required>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Configuration</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Next Number <span class="text-danger">*</span></label>
                        <input type="number" name="next_number" class="form-control" value="{{ old('next_number', $numberSeries?->next_number ?? 1) }}" min="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $numberSeries?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $numberSeries?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($numberSeries) ? 'Update' : 'Create' }} Number Series</button>
                    <a href="{{ route('admin.masters.number-series.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

