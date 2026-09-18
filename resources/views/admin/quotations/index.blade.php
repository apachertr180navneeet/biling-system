@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Quotation Master</h4>
        <div>
            <a href="{{ route('admin.quotations.create-vehicle') }}" class="btn btn-primary me-2">
                <i class="bx bx-plus"></i> New Vehicle Quotation
            </a>
            <a href="{{ route('admin.quotations.create-parts') }}" class="btn btn-success">
                <i class="bx bx-plus"></i> New Parts Quotation
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.quotations.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by Quotation No, Customer Name or Mobile" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="type" class="form-select no-select2">
                            <option value="">-- All Types --</option>
                            <option value="vehicle" {{ request('type') === 'vehicle' ? 'selected' : '' }}>Vehicle Quotations</option>
                            <option value="parts" {{ request('type') === 'parts' ? 'selected' : '' }}>Parts Quotations</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search"></i> Search</button>
                            <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ACTIONS</th>
                        <th>Quotation No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Customer</th>
                        <th>Items/Vehicle</th>
                        <th>Taxable Amount</th>
                        <th>GST Amount</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                    <tr>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-action-dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu action-dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.quotations.show', $q) }}">
                                            <i class="bx bx-show text-info"></i> View Quotation
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.quotations.pdf', $q) }}" target="_blank">
                                            <i class="bx bxs-file-pdf text-danger"></i> Download PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="directPrintPdf('{{ route('admin.quotations.pdf', $q) }}')">
                                            <i class="bx bx-printer text-secondary"></i> Print PDF
                                        </a>
                                    </li>
                                    @if($q->type === 'vehicle')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.quotations.edit-vehicle', $q) }}">
                                            <i class="bx bx-edit text-primary"></i> Edit Quotation
                                        </a>
                                    </li>
                                    @else
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.quotations.edit-parts', $q) }}">
                                            <i class="bx bx-edit text-primary"></i> Edit Quotation
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.quotations.whatsapp', $q) }}" target="_blank">
                                            <i class="bx bxl-whatsapp text-success"></i> Send WhatsApp
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.quotations.destroy', $q) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?')" style="display:block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                                <i class="bx bx-trash text-danger"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.quotations.show', $q) }}" class="fw-bold">
                                {{ $q->quotation_number }}
                            </a>
                        </td>
                        <td>{{ $q->quotation_date->format('d-m-Y') }}</td>
                        <td>
                            @if($q->type === 'vehicle')
                                <span class="badge bg-primary">Vehicle</span>
                            @else
                                <span class="badge bg-success">Parts</span>
                            @endif
                        </td>
                        <td>
                            {{ $q->customer_name }}
                            @if($q->customer_mobile) <br><small class="text-muted">{{ $q->customer_mobile }}</small> @endif
                        </td>
                        <td>
                            @if($q->type === 'vehicle')
                                {{ $q->vehicleMaster->variant_name ?? '-' }}
                            @else
                                {{ $q->items->count() }} Parts
                            @endif
                        </td>
                        <td>{{ number_format($q->taxable_amount, 2) }}</td>
                        <td>
                            @if($q->tax_regime === 'cgst_sgst')
                                {{ number_format($q->cgst_amount + $q->sgst_amount, 2) }} (CGST+SGST)
                            @else
                                {{ number_format($q->igst_amount, 2) }} (IGST)
                            @endif
                        </td>
                        <td class="fw-bold">{{ number_format($q->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No quotations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $quotations->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
