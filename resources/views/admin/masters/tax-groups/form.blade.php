@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-layer"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($taxGroup) ? 'Edit Tax Group' : 'Create Tax Group' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.masters.tax-groups.index') }}">Tax Groups</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($taxGroup) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.masters.tax-groups.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($taxGroup) ? route('admin.masters.tax-groups.update', $taxGroup) : route('admin.masters.tax-groups.store') }}" method="POST">
                @csrf
                @if(isset($taxGroup)) @method('PUT') @endif
                
                <div class="m-section-divider">Tax Group Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $taxGroup?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $taxGroup?->description) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Applicable Taxes</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        @error('tax_ids') <div class="text-danger mb-2">{{ $message }}</div> @enderror
                        <div class="row g-3">
                            @foreach($taxes as $tax)
                            <div class="col-md-3">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="tax_ids[]" value="{{ $tax->id }}" id="tax_{{ $tax->id }}" {{ in_array($tax->id, old('tax_ids', $assignedTaxes ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="tax_{{ $tax->id }}">
                                        {{ $tax->name }} 
                                        <span class="badge bg-label-info float-end">{{ $tax->rate }}{{ $tax->type == 'percentage' ? '%' : '' }}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($taxGroup) ? 'Update' : 'Create' }} Tax Group</button>
                    <a href="{{ route('admin.masters.tax-groups.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

