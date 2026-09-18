@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">Vendor: {{ $vendor->company_name }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Procurement</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.procurement.vendors.index') }}">Vendors</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">View</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('vendors.edit'))
            <a href="{{ route('admin.procurement.vendors.edit', $vendor) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('admin.procurement.vendors.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Basic Information</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Company Name</th><td>{{ $vendor->company_name }}</td></tr>
                        <tr><th>Hotel</th><td>{{ $vendor->hotel->name ?? '-' }}</td></tr>
                        <tr><th>Category</th><td>{{ $vendor->category->name ?? '-' }}</td></tr>
                        <tr><th>Vendor Type</th><td><span class="badge bg-label-primary">{{ ucfirst($vendor->vendor_type) }}</span></td></tr>
                        <tr><th>Contact Person</th><td>{{ $vendor->contact_person ?? '-' }}</td></tr>
                        <tr><th>Rating</th><td>{{ $vendor->rating ? ucfirst($vendor->rating) : '-' }}</td></tr>
                        <tr><th>Status</th>
                            <td>
                                @if($vendor->status == 'active')
                                    <span class="badge bg-label-success">Active</span>
                                @elseif($vendor->status == 'blacklisted')
                                    <span class="badge bg-label-danger">Blacklisted</span>
                                @else
                                    <span class="badge bg-label-warning">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Contact Details</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Email</th><td>{{ $vendor->email ?? '-' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $vendor->phone ?? '-' }}</td></tr>
                        <tr><th>Address</th><td>{{ $vendor->address ?? '-' }}</td></tr>
                        <tr><th>City</th><td>{{ $vendor->city ?? '-' }}</td></tr>
                        <tr><th>State</th><td>{{ $vendor->state ?? '-' }}</td></tr>
                        <tr><th>Pincode</th><td>{{ $vendor->pincode ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Tax & Compliance</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">GSTIN</th><td>{{ $vendor->gstin ?? '-' }}</td></tr>
                        <tr><th>PAN</th><td>{{ $vendor->pan ?? '-' }}</td></tr>
                        <tr><th>CIN</th><td>{{ $vendor->cin ?? '-' }}</td></tr>
                        <tr><th>TAN</th><td>{{ $vendor->tan ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Bank Details</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Bank Name</th><td>{{ $vendor->bank_name ?? '-' }}</td></tr>
                        <tr><th>Account Number</th><td>{{ $vendor->bank_account_number ?? '-' }}</td></tr>
                        <tr><th>IFSC</th><td>{{ $vendor->bank_ifsc ?? '-' }}</td></tr>
                        <tr><th>Branch</th><td>{{ $vendor->bank_branch ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Terms & Agreement</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Credit Limit</th><td>₹{{ number_format($vendor->credit_limit, 2) }}</td></tr>
                        <tr><th>Payment Terms</th><td>{{ $vendor->payment_terms ?? '-' }}</td></tr>
                        <tr><th>Agreement Start</th><td>{{ $vendor->agreement_start_date ? $vendor->agreement_start_date->format('d-m-Y') : '-' }}</td></tr>
                        <tr><th>Agreement End</th><td>{{ $vendor->agreement_end_date ? $vendor->agreement_end_date->format('d-m-Y') : '-' }}</td></tr>
                        <tr><th>Notes</th><td>{{ $vendor->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
