@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-file"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($gst) ? 'Edit GST Return' : 'Create GST Return' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.gst.index') }}">GST Returns</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($gst) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.gst.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($gst) ? route('admin.finance.gst.update', $gst) : route('admin.finance.gst.store') }}" method="POST">
                @csrf
                @if(isset($gst)) @method('PUT') @endif

                <div class="m-section-divider">Return Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Period <span class="text-danger">*</span></label>
                        <input type="month" name="period" class="form-control" value="{{ old('period', $gst?->period) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Return Type <span class="text-danger">*</span></label>
                        <select name="return_type" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="gstr1" {{ old('return_type', $gst?->return_type) == 'gstr1' ? 'selected' : '' }}>GSTR-1</option>
                            <option value="gstr3b" {{ old('return_type', $gst?->return_type) == 'gstr3b' ? 'selected' : '' }}>GSTR-3B</option>
                            <option value="gstr9" {{ old('return_type', $gst?->return_type) == 'gstr9' ? 'selected' : '' }}>GSTR-9</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Filing Date</label>
                        <input type="date" name="filing_date" class="form-control" value="{{ old('filing_date', $gst?->filing_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="m-section-divider">Tax Summary</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Taxable Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_taxable_value" class="form-control" value="{{ old('total_taxable_value', $gst?->total_taxable_value) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total CGST</label>
                        <input type="number" step="0.01" name="total_cgst" class="form-control" value="{{ old('total_cgst', $gst?->total_cgst ?? 0) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total SGST</label>
                        <input type="number" step="0.01" name="total_sgst" class="form-control" value="{{ old('total_sgst', $gst?->total_sgst ?? 0) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total IGST</label>
                        <input type="number" step="0.01" name="total_igst" class="form-control" value="{{ old('total_igst', $gst?->total_igst ?? 0) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total Cess</label>
                        <input type="number" step="0.01" name="total_cess" class="form-control" value="{{ old('total_cess', $gst?->total_cess ?? 0) }}">
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($gst) ? 'Update' : 'Create' }} Return</button>
                    <a href="{{ route('admin.finance.gst.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
