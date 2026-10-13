@extends('admin.layouts.app')
@section('style')
<style>
/* Custom Modal Styling for clean contrast & crisp table layout */
#addItemModal .modal-header {
    background-color: #233446 !important;
    color: #ffffff !important;
    padding: 1rem 1.5rem;
}
#addItemModal .modal-title {
    color: #ffffff !important;
    font-weight: 600;
    font-size: 1.1rem;
}
#addItemModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.8;
}
#addItemModal .btn-close:hover {
    opacity: 1;
}
#addItemModal .table-responsive {
    border: 1px solid #d9dee3;
    border-radius: 0.375rem;
}
#addItemModal #modalPartsTable {
    margin-bottom: 0;
}
#addItemModal #modalPartsTable thead th {
    background-color: #1e293b !important;
    color: #ffffff !important;
    position: sticky;
    top: 0;
    z-index: 10;
    padding: 12px 14px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #0f172a !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
#addItemModal #modalPartsTable tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    background-color: #ffffff;
}
#addItemModal #modalPartsTable tbody tr:nth-of-type(even) td {
    background-color: #f8fafc;
}
#addItemModal #modalPartsTable tbody tr:hover td {
    background-color: #f1f5f9;
}
#addItemModal .btn-add-modal-part {
    white-space: nowrap;
    font-weight: 600;
}
.spare-parts-modal .modal-content {
    border-radius: 12px;
    overflow: hidden;
}
.spare-parts-modal .modal-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.modal-search-box {
    background: #f1f5f9;
    border-radius: 8px;
    padding: 4px;
    border: 1px solid #cbd5e1;
}
.modal-search-box input {
    background: transparent;
    font-weight: 500;
}
.modal-info-pill {
    display: inline-block;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid #bfdbfe;
}
.parts-table-wrap {
    max-height: 420px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
.part-no-text {
    font-family: monospace;
    font-weight: 700;
    color: #4f46e5;
    background: #eef2ff;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
}
.part-name-text {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
}
.badge-stock-available {
    background: #dcfce7;
    color: #15803d;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    border: 1px solid #bbf7d0;
    display: inline-block;
}
.badge-stock-out {
    background: #fee2e2;
    color: #b91c1c;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    border: 1px solid #fecaca;
    display: inline-block;
}
.btn-modal-add {
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    color: #ffffff;
    font-weight: 600;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
.btn-modal-add:hover {
    color: #ffffff;
    filter: brightness(1.1);
}
.row-selected td {
    background-color: #f0fdf4 !important;
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Edit Part Sales Invoice #{{ $partSalesInvoice->invoice_number }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.part-sales-invoices.show', $partSalesInvoice) }}" class="btn btn-outline-info">
                <i class="bx bx-show me-1"></i> View Invoice
            </a>
            <a href="{{ route('admin.part-sales-invoices.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.part-sales-invoices.update', $partSalesInvoice->id) }}" id="invoiceForm">
                @csrf
                @method('PUT')
                
                <h5 class="card-title text-primary mb-3"><i class="bx bx-user me-1"></i> Customer Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Select Customer (Existing)</label>
                        <div class="input-group">
                            <select id="customer_select" name="customer_id" class="form-select">
                                <option value="">-- New Customer / Walk-in --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                        {{ old('customer_id', $partSalesInvoice->customer_id) == $c->id ? 'selected' : '' }}
                                        data-name="{{ $c->name }}"
                                        data-mobile="{{ $c->phone }}"
                                        data-address="{{ $c->address }}"
                                        data-gstin="{{ $c->gstin }}"
                                        data-pan="{{ $c->pan_no }}">
                                    {{ $c->name }} ({{ $c->phone }})
                                </option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal">
                                <i class="bx bx-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $partSalesInvoice->customer_name) }}" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" id="customer_mobile" name="customer_mobile" class="form-control @error('customer_mobile') is-invalid @enderror" value="{{ old('customer_mobile', $partSalesInvoice->customer_mobile) }}">
                        @error('customer_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">GSTIN (Optional)</label>
                        <input type="text" id="customer_gstin" name="customer_gstin" class="form-control @error('customer_gstin') is-invalid @enderror" value="{{ old('customer_gstin', $partSalesInvoice->customer_gstin) }}" placeholder="15-digit GSTIN" maxlength="15">
                        @error('customer_gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PAN Number (Optional)</label>
                        <input type="text" id="customer_pan" name="customer_pan" class="form-control @error('customer_pan') is-invalid @enderror" value="{{ old('customer_pan', $partSalesInvoice->customer_pan) }}" placeholder="10-digit PAN" maxlength="10">
                        @error('customer_pan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Place of Supply <span class="text-danger">*</span></label>
                        <input type="text" id="place_of_supply" name="place_of_supply" class="form-control @error('place_of_supply') is-invalid @enderror" value="{{ old('place_of_supply', $partSalesInvoice->place_of_supply ?? 'Rajasthan') }}" required>
                        @error('place_of_supply')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Billing Address</label>
                        <textarea id="customer_address" name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" rows="2">{{ old('customer_address', $partSalesInvoice->customer_address) }}</textarea>
                        @error('customer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="card-title text-primary mb-3"><i class="bx bx-receipt me-1"></i> Invoice Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number', $partSalesInvoice->invoice_number) }}" required>
                        @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', \Carbon\Carbon::parse($partSalesInvoice->invoice_date)->format('Y-m-d')) }}" required>
                        @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-select no-select2 @error('payment_mode') is-invalid @enderror" required>
                            <option value="Cash" {{ old('payment_mode', $partSalesInvoice->payment_mode) === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="UPI / Online" {{ old('payment_mode', $partSalesInvoice->payment_mode) === 'UPI / Online' ? 'selected' : '' }}>UPI / Online</option>
                            <option value="Card" {{ old('payment_mode', $partSalesInvoice->payment_mode) === 'Card' ? 'selected' : '' }}>Card</option>
                        </select>
                        @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tax Regime <span class="text-danger">*</span></label>
                        <select name="tax_regime" id="tax_regime" class="form-select no-select2" required>
                            <option value="cgst_sgst" {{ old('tax_regime', $partSalesInvoice->tax_regime ?? 'cgst_sgst') === 'cgst_sgst' ? 'selected' : '' }}>CGST + SGST</option>
                            <option value="igst" {{ old('tax_regime', $partSalesInvoice->tax_regime) === 'igst' ? 'selected' : '' }}>IGST</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-primary mb-0"><i class="bx bx-package me-1"></i> Invoice Items (Parts)</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="btnOpenSearchModal">
                        <i class="bx bx-search me-1"></i> Search & Add Item (Modal)
                    </button>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead>
                            <tr class="table-dark">
                                <th style="width: 30%;">Part Name / Number <span class="text-danger">*</span></th>
                                <th style="width: 10%; text-align: center;">Stock Available</th>
                                <th style="width: 8%; text-align: center;">Qty <span class="text-danger">*</span></th>
                                <th style="width: 12%;">Rate <span class="text-danger">*</span></th>
                                <th style="width: 12%;">GST Type <span class="text-danger">*</span></th>
                                <th style="width: 10%;">GST %</th>
                                <th style="width: 13%;">Total Amount</th>
                                <th style="width: 5%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            @foreach($partSalesInvoice->items as $idx => $item)
                            @php
                                $stockAvailable = 0;
                                foreach($spareParts as $sp) {
                                    if ($sp->id == $item->spare_part_id) {
                                        $stockAvailable = $sp->qty_available + $item->quantity;
                                        break;
                                    }
                                }
                                $itemTaxPct = number_format($item->tax_percentage, 2, '.', '');
                                $itemRate = number_format($item->rate, 2, '.', '');
                            @endphp
                            <tr class="item-row" data-part-id="{{ $item->spare_part_id }}">
                                <td>
                                    <input type="hidden" name="items[{{ $idx }}][spare_part_id]" class="part-id-input" value="{{ $item->spare_part_id }}" required>
                                    <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="{{ $item->sparePart->part_no ?? '' }} - {{ $item->sparePart->name ?? '' }}" required>
                                    <div class="mt-2">
                                        <input type="text" name="items[{{ $idx }}][serial_no_warranty_notes]" class="form-control form-control-sm notes-input" placeholder="Serial No. / Warranty Notes (Optional)" value="{{ $item->serial_no_warranty_notes }}">
                                    </div>
                                </td>
                                <td class="text-center bg-light">
                                    <span class="stock-badge fw-bold {{ $stockAvailable > 0 ? 'text-success' : 'text-danger' }}">{{ $stockAvailable }}</span>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $idx }}][quantity]" class="form-control qty-input text-center fw-bold" min="1" value="{{ $item->quantity }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][rate]" class="form-control rate-input fw-semibold" min="0" value="{{ $itemRate }}" data-entered-rate="{{ $itemRate }}" required>
                                </td>
                                <td>
                                    <select name="items[{{ $idx }}][gst_type]" class="form-select gst-type-select no-select2" required>
                                        <option value="exclusive" selected>Exclusive</option>
                                        <option value="inclusive">Inclusive</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{ $idx }}][tax_percentage]" class="form-select tax-select no-select2" required>
                                        <option value="0.00" {{ $itemTaxPct === '0.00' ? 'selected' : '' }}>0%</option>
                                        <option value="5.00" {{ $itemTaxPct === '5.00' ? 'selected' : '' }}>5%</option>
                                        <option value="12.00" {{ $itemTaxPct === '12.00' ? 'selected' : '' }}>12%</option>
                                        <option value="18.00" {{ $itemTaxPct === '18.00' ? 'selected' : '' }}>18%</option>
                                        <option value="28.00" {{ $itemTaxPct === '28.00' ? 'selected' : '' }}>28%</option>
                                    </select>
                                </td>
                                <td class="bg-light">
                                    <input type="text" class="form-control line-total bg-transparent border-0 fw-bold text-end" readonly value="{{ number_format($item->amount, 2, '.', '') }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="noItemsNotice" class="{{ count($partSalesInvoice->items) > 0 ? 'd-none' : '' }}">
                                <td colspan="8" class="text-center p-4 text-muted bg-light">
                                    <i class="bx bx-package fs-2 mb-2 d-block text-primary"></i>
                                    No items added yet. Click <strong>"Search & Add Item (Modal)"</strong> below to select spare parts.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4 d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btnOpenSearchModalBottom">
                        <i class="bx bx-search me-1"></i> Search & Add Item (Modal)
                    </button>
                </div>

                <h5 class="card-title text-primary mb-3"><i class="bx bx-calculator me-1"></i> Payment Summary</h5>
                <div class="row g-3 mb-4 bg-light p-3 rounded border border-light-subtle">
                    <div class="col-md-3">
                        <label class="form-label">Taxable Amount (INR)</label>
                        <input type="text" id="summary_taxable" class="form-control bg-white" readonly value="{{ number_format($partSalesInvoice->taxable_amount, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3 {{ ($partSalesInvoice->tax_regime ?? 'cgst_sgst') === 'igst' ? 'd-none' : '' }}" id="summary_cgst_sgst_fields">
                        <label class="form-label">CGST Amount (INR)</label>
                        <input type="text" id="summary_cgst" class="form-control bg-white" readonly value="{{ number_format($partSalesInvoice->cgst_amount, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3 {{ ($partSalesInvoice->tax_regime ?? 'cgst_sgst') === 'igst' ? 'd-none' : '' }}" id="summary_cgst_sgst_fields2">
                        <label class="form-label">SGST Amount (INR)</label>
                        <input type="text" id="summary_sgst" class="form-control bg-white" readonly value="{{ number_format($partSalesInvoice->sgst_amount, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3 {{ ($partSalesInvoice->tax_regime ?? 'cgst_sgst') === 'igst' ? '' : 'd-none' }}" id="summary_igst_field">
                        <label class="form-label">IGST Amount (INR)</label>
                        <input type="text" id="summary_igst" class="form-control bg-white" readonly value="{{ number_format($partSalesInvoice->igst_amount, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Round Off (INR)</label>
                        <input type="text" id="summary_round" class="form-control bg-white" readonly value="{{ number_format($partSalesInvoice->round_off, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Grand Total (INR)</label>
                        <input type="text" id="summary_grand" class="form-control bg-white fw-bold text-success" readonly value="{{ number_format($partSalesInvoice->total_amount, 2, '.', '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Previous Balance (INR)</label>
                        <input type="number" step="0.01" name="previous_balance" id="previous_balance" class="form-control" value="{{ number_format($partSalesInvoice->previous_balance, 2, '.', '') }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Received Amount (INR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="received_amount" id="received_amount" class="form-control fw-bold" value="{{ number_format($partSalesInvoice->received_amount, 2, '.', '') }}" required min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-danger">Current Balance (INR)</label>
                        <input type="text" id="summary_current_balance" class="form-control bg-white fw-bold text-danger" readonly value="{{ number_format($partSalesInvoice->current_balance, 2, '.', '') }}">
                    </div>
                </div>

                <!-- Auto-Appearing Customer Ledger Card -->
                <div id="customer_ledger_card" class="card mb-4 border border-primary-subtle shadow-sm d-none" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="bx bx-book-content me-1"></i> Customer Ledger Summary (<span id="ledger_customer_name">Customer</span>)
                            </h6>
                            <a id="view_full_ledger_btn" href="#" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bx bx-link-external me-1"></i> View Full Customer Ledger
                            </a>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-md-4">
                                <div class="bg-white p-2 rounded border">
                                    <small class="text-muted d-block">Total Invoiced Amount</small>
                                    <span class="fw-bold fs-6 text-dark" id="lbl_ledger_total">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white p-2 rounded border">
                                    <small class="text-muted d-block">Total Paid Amount</small>
                                    <span class="fw-bold fs-6 text-success" id="lbl_ledger_paid">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white p-2 rounded border">
                                    <small class="text-muted d-block">Net Outstanding Balance</small>
                                    <span class="fw-bold fs-6 text-danger" id="lbl_ledger_outstanding">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-save me-1"></i> Update Invoice
                    </button>
                    <a href="{{ route('admin.part-sales-invoices.show', $partSalesInvoice) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="quickAddCustomerForm" method="POST" action="javascript:void(0);">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="modalErrorAlert"></div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="modal_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="phone" id="modal_phone" class="form-control" maxlength="10" placeholder="10 digits">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="modal_type" class="form-select no-select2">
                                <option value="individual">Individual</option>
                                <option value="corporate">Corporate</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GSTIN (Optional)</label>
                            <input type="text" name="gstin" id="modal_gstin" class="form-control" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PAN No (Optional)</label>
                            <input type="text" name="pan_no" id="modal_pan_no" class="form-control" maxlength="10">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Billing Address</label>
                            <textarea name="address" id="modal_address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveCustomer">Save Customer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Item Search & Add Modal -->
<div class="modal fade spare-parts-modal" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1050px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">
                    <i class="bx bx-package me-2" style="color: #a5b4fc; font-size: 1.4rem;"></i>Select Spare Parts
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search Box & Notice -->
                <div class="row g-3 mb-3 align-items-center" id="modalSearchContainer">
                    <div class="col-md-6">
                        <div class="input-group modal-search-box">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bx bx-search fs-5"></i></span>
                            <input type="text" id="modalPartSearch" class="form-control border-0 py-2" placeholder="Type Part No, Name, or HSN Code to search...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="modal-info-pill">
                            <i class="bx bx-info-circle me-2 fs-5"></i> SELECT ITEMS USING CHECKBOX OR ENTER QTY (> 0), THEN CLICK ADD.
                        </div>
                    </div>
                </div>

                <!-- Spare Parts Table -->
                <div class="parts-table-wrap" id="modalTableWrapper">
                    <table class="table table-hover align-middle mb-0" id="modalPartsTable">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="checkAllParts">
                                </th>
                                <th style="width: 140px;">PART NO.</th>
                                <th>PART NAME</th>
                                <th style="width: 150px; text-align: center;">STOCK STATUS</th>
                                <th style="width: 120px; text-align: center;">RATE (₹)</th>
                                <th style="width: 90px; text-align: center;">QTY</th>
                            </tr>
                        </thead>
                        <tbody id="modalPartsBody">
                            @foreach($spareParts as $p)
                            <tr class="modal-part-row" 
                                data-id="{{ $p->id }}" 
                                data-name="{{ strtolower($p->name) }}" 
                                data-partno="{{ strtolower($p->part_no) }}" 
                                data-hsn="{{ strtolower($p->hsn_code ?? $p->hsn_sac_code ?? '') }}"
                                data-displayname="{{ $p->part_no }} - {{ $p->name }}"
                                data-price="{{ number_format($p->selling_price, 2, '.', '') }}"
                                data-stock="{{ $p->qty_available }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input modal-part-checkbox">
                                </td>
                                <td>
                                    <span class="part-no-text">{{ $p->part_no }}</span>
                                </td>
                                <td>
                                    <span class="part-name-text">{{ $p->name }}</span>
                                </td>
                                <td class="text-center">
                                    @if($p->qty_available > 0)
                                        <span class="badge-stock-available">{{ $p->qty_available }} AVAILABLE</span>
                                    @else
                                        <span class="badge-stock-out">OUT OF STOCK</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="number" step="0.01" class="form-control form-control-sm text-center modal-part-rate fw-bold" value="{{ number_format($p->selling_price, 2, '.', '') }}" min="0" style="max-width: 100px; margin: 0 auto; border-radius: 6px;">
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center modal-part-qty fw-bold" value="1" min="0" style="max-width: 75px; margin: 0 auto; border-radius: 6px;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="fw-semibold text-secondary" id="selectedPartsCount">0 item(s) selected</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-modal-add" id="btnAddSelectedModalParts">
                        <i class="bx bx-plus me-1"></i> Add Selected Items to Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var customerSelect = document.getElementById('customer_select');
    var customerNameInput = document.getElementById('customer_name');
    var customerMobileInput = document.getElementById('customer_mobile');
    var customerAddressInput = document.getElementById('customer_address');
    var customerGstInput = document.getElementById('customer_gstin');
    var customerPanInput = document.getElementById('customer_pan');
    
    $(customerSelect).on('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt && opt.value) {
            customerNameInput.value = opt.getAttribute('data-name') || '';
            customerMobileInput.value = opt.getAttribute('data-mobile') || '';
            customerAddressInput.value = opt.getAttribute('data-address') || '';
            customerGstInput.value = opt.getAttribute('data-gstin') || '';
            customerPanInput.value = opt.getAttribute('data-pan') || '';
        }
        fetchCustomerLedgerSummary();
    });

    function fetchCustomerLedgerSummary() {
        var customerId = $('#customer_select').val();
        var customerName = $.trim($('#customer_name').val());
        var customerMobile = $.trim($('#customer_mobile').val());

        if (!customerId && customerName.length < 2 && customerMobile.length < 3) {
            $('#customer_ledger_card').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('admin.customers.ledger-summary') }}",
            type: 'GET',
            data: {
                customer_id: customerId,
                customer_name: customerName,
                customer_mobile: customerMobile
            },
            success: function(resp) {
                if (resp.success) {
                    $('#ledger_customer_name').text(resp.customer_name);
                    $('#lbl_ledger_total').text('₹' + parseFloat(resp.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#lbl_ledger_paid').text('₹' + parseFloat(resp.paid_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    var bal = parseFloat(resp.outstanding_balance);
                    $('#lbl_ledger_outstanding').text('₹' + bal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    if (bal > 0) {
                        $('#lbl_ledger_outstanding').removeClass('text-success text-muted').addClass('text-danger');
                    } else {
                        $('#lbl_ledger_outstanding').removeClass('text-danger').addClass('text-success');
                    }

                    if (resp.ledger_url) {
                        $('#view_full_ledger_btn').attr('href', resp.ledger_url).removeClass('d-none');
                    } else {
                        $('#view_full_ledger_btn').addClass('d-none');
                    }

                    $('#customer_ledger_card').removeClass('d-none');
                } else {
                    $('#customer_ledger_card').addClass('d-none');
                }
            },
            error: function() {
                $('#customer_ledger_card').addClass('d-none');
            }
        });
    }

    var ledgerTimer = null;
    $(document).on('input keyup blur', '#customer_name, #customer_mobile', function(){
        clearTimeout(ledgerTimer);
        ledgerTimer = setTimeout(fetchCustomerLedgerSummary, 300);
    });
    fetchCustomerLedgerSummary();

    var itemsContainer = document.getElementById('itemsContainer');
    var btnOpenSearchModal = document.getElementById('btnOpenSearchModal');
    var btnOpenSearchModalBottom = document.getElementById('btnOpenSearchModalBottom');
    var itemIndex = {{ count($partSalesInvoice->items) + 10 }};

    var itemModalEl = document.getElementById('addItemModal');
    var itemModal = new bootstrap.Modal(itemModalEl);
    var modalPartSearch = document.getElementById('modalPartSearch');
    var btnAddSelectedModalParts = document.getElementById('btnAddSelectedModalParts');
    var selectedPartsCount = document.getElementById('selectedPartsCount');
    var checkAllParts = document.getElementById('checkAllParts');

    function checkNoItemsNotice() {
        var noNotice = document.getElementById('noItemsNotice');
        var rows = itemsContainer.querySelectorAll('.item-row');
        if (noNotice) {
            if (rows.length > 0) {
                noNotice.classList.add('d-none');
            } else {
                noNotice.classList.remove('d-none');
            }
        }
    }

    function createRow(partId = '', partName = '', qty = 1, rate = 0.00, gstType = 'exclusive', taxPct = '18.00', notes = '', stock = 0) {
        var row = document.createElement('tr');
        row.className = 'item-row';
        row.setAttribute('data-part-id', partId);
        row.innerHTML = `
            <td>
                <input type="hidden" name="items[${itemIndex}][spare_part_id]" class="part-id-input" value="${partId}" required>
                <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="${partName}" required>
                <div class="mt-2">
                    <input type="text" name="items[${itemIndex}][serial_no_warranty_notes]" class="form-control form-control-sm notes-input" placeholder="Serial No. / Warranty Notes (Optional)" value="${notes}">
                </div>
            </td>
            <td class="text-center bg-light">
                <span class="stock-badge fw-bold ${stock > 0 ? 'text-success' : 'text-danger'}">${stock}</span>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input text-center fw-bold" min="1" value="${qty}" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control rate-input fw-semibold" min="0" value="${parseFloat(rate).toFixed(2)}" data-entered-rate="${parseFloat(rate).toFixed(2)}" required>
            </td>
            <td>
                <select name="items[${itemIndex}][gst_type]" class="form-select gst-type-select no-select2" required>
                    <option value="exclusive" ${gstType === 'exclusive' ? 'selected' : ''}>Exclusive</option>
                    <option value="inclusive" ${gstType === 'inclusive' ? 'selected' : ''}>Inclusive</option>
                </select>
            </td>
            <td>
                <select name="items[${itemIndex}][tax_percentage]" class="form-select tax-select no-select2" required>
                    <option value="0.00" ${taxPct == '0.00' ? 'selected' : ''}>0%</option>
                    <option value="5.00" ${taxPct == '5.00' ? 'selected' : ''}>5%</option>
                    <option value="12.00" ${taxPct == '12.00' ? 'selected' : ''}>12%</option>
                    <option value="18.00" ${taxPct == '18.00' ? 'selected' : ''}>18%</option>
                    <option value="28.00" ${taxPct == '28.00' ? 'selected' : ''}>28%</option>
                </select>
            </td>
            <td class="bg-light">
                <input type="text" class="form-control line-total bg-transparent border-0 fw-bold text-end" readonly value="0.00">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove"><i class="bx bx-trash"></i></button>
            </td>
        `;
        itemIndex++;
        itemsContainer.appendChild(row);
        bindRowEvents(row);
        checkNoItemsNotice();
        return row;
    }

    function openItemSearchModal() {
        document.querySelectorAll('.modal-part-checkbox').forEach(function(cb) { cb.checked = false; });
        document.querySelectorAll('.modal-part-row').forEach(function(row) { 
            row.classList.remove('row-selected'); 
            var qtyIn = row.querySelector('.modal-part-qty');
            if (qtyIn) qtyIn.value = 1;
        });
        if (checkAllParts) checkAllParts.checked = false;
        updateSelectedPartsCount();

        modalPartSearch.value = '';
        filterModalParts();
        itemModal.show();
        setTimeout(function() { modalPartSearch.focus(); }, 400);
    }

    if (btnOpenSearchModal) btnOpenSearchModal.addEventListener('click', openItemSearchModal);
    if (btnOpenSearchModalBottom) btnOpenSearchModalBottom.addEventListener('click', openItemSearchModal);

    function filterModalParts() {
        var query = modalPartSearch.value.trim().toLowerCase();
        var rows = document.querySelectorAll('.modal-part-row');

        rows.forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var partNo = row.getAttribute('data-partno') || '';
            var hsn = row.getAttribute('data-hsn') || '';
            if (!query || name.includes(query) || partNo.includes(query) || hsn.includes(query)) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });
    }

    modalPartSearch.addEventListener('input', filterModalParts);

    if (checkAllParts) {
        checkAllParts.addEventListener('change', function() {
            var isChecked = this.checked;
            document.querySelectorAll('.modal-part-row:not(.d-none)').forEach(function(row) {
                var cb = row.querySelector('.modal-part-checkbox');
                var qtyInput = row.querySelector('.modal-part-qty');
                if (cb) cb.checked = isChecked;
                if (isChecked) {
                    row.classList.add('row-selected');
                    if (qtyInput && parseInt(qtyInput.value) < 1) qtyInput.value = 1;
                } else {
                    row.classList.remove('row-selected');
                }
            });
            updateSelectedPartsCount();
        });
    }

    document.getElementById('modalPartsBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('modal-part-checkbox')) {
            var row = e.target.closest('.modal-part-row');
            var qtyInput = row.querySelector('.modal-part-qty');
            if (e.target.checked) {
                row.classList.add('row-selected');
                if (qtyInput && parseInt(qtyInput.value) < 1) qtyInput.value = 1;
            } else {
                row.classList.remove('row-selected');
            }
            updateSelectedPartsCount();
        }
    });

    document.getElementById('modalPartsBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('modal-part-qty')) {
            var row = e.target.closest('.modal-part-row');
            var cb = row.querySelector('.modal-part-checkbox');
            var val = parseInt(e.target.value) || 0;
            if (val > 0) {
                if (cb) cb.checked = true;
                row.classList.add('row-selected');
            } else {
                if (cb) cb.checked = false;
                row.classList.remove('row-selected');
            }
            updateSelectedPartsCount();
        }
    });

    function updateSelectedPartsCount() {
        var count = 0;
        document.querySelectorAll('.modal-part-row').forEach(function(row) {
            var cb = row.querySelector('.modal-part-checkbox');
            var qtyInput = row.querySelector('.modal-part-qty');
            if (cb && cb.checked && qtyInput && parseInt(qtyInput.value) > 0) {
                count++;
            }
        });
        if (selectedPartsCount) {
            selectedPartsCount.textContent = count + ' item(s) selected';
        }
    }

    if (btnAddSelectedModalParts) {
        btnAddSelectedModalParts.addEventListener('click', function() {
            var selectedRows = [];
            document.querySelectorAll('.modal-part-row').forEach(function(row) {
                var cb = row.querySelector('.modal-part-checkbox');
                var qtyInput = row.querySelector('.modal-part-qty');
                if (cb && cb.checked && qtyInput && parseInt(qtyInput.value) > 0) {
                    selectedRows.push(row);
                }
            });

            if (selectedRows.length === 0) {
                alert('Please select at least one item using the checkbox or quantity.');
                return;
            }

            selectedRows.forEach(function(row) {
                var partId = row.getAttribute('data-id');
                var partName = row.getAttribute('data-displayname') || row.getAttribute('data-name');
                var stock = parseInt(row.getAttribute('data-stock')) || 0;
                var qtyInput = row.querySelector('.modal-part-qty');
                var rateInput = row.querySelector('.modal-part-rate');
                var qty = parseInt(qtyInput.value) || 0;
                var rate = parseFloat(rateInput.value) || parseFloat(row.getAttribute('data-price')) || 0;

                // Check if item already exists in table
                var existingRow = itemsContainer.querySelector('tr.item-row[data-part-id="' + partId + '"]');
                if (existingRow) {
                    var existingQtyInput = existingRow.querySelector('.qty-input');
                    var currentQty = parseInt(existingQtyInput.value) || 0;
                    existingQtyInput.value = currentQty + qty;
                    calculateRow(existingRow);
                } else {
                    var newRow = createRow(partId, partName, qty, rate, 'exclusive', '18.00', '', stock);
                    calculateRow(newRow);
                }

                qtyInput.value = 1;
            });

            updateSelectedPartsCount();
            checkNoItemsNotice();
            calculateSummary();
            itemModal.hide();
        });
    }

    // Remove Row Event
    itemsContainer.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.btn-remove-row');
        if (removeBtn) {
            var row = removeBtn.closest('.item-row');
            row.remove();
            checkNoItemsNotice();
            calculateSummary();
        }
    });

    function convertRowInclusiveToExclusive(row) {
        var rateInput = row.querySelector('.rate-input');
        var gstTypeSelect = row.querySelector('.gst-type-select');
        var taxSelect = row.querySelector('.tax-select');

        var gstType = gstTypeSelect.value;
        var enteredRate = parseFloat(rateInput.dataset.enteredRate) || parseFloat(rateInput.value) || 0;

        if (gstType === 'inclusive') {
            var taxPct = parseFloat(taxSelect.value) || 0;
            var baseRate = enteredRate / (1 + (taxPct / 100));
            rateInput.value = baseRate.toFixed(2);
        } else {
            rateInput.value = enteredRate.toFixed(2);
        }
        calculateRow(row);
    }

    function bindRowEvents(row) {
        var qtyInput = row.querySelector('.qty-input');
        var rateInput = row.querySelector('.rate-input');
        var gstTypeSelect = row.querySelector('.gst-type-select');
        var taxSelect = row.querySelector('.tax-select');

        qtyInput.addEventListener('input', function() {
            calculateRow(row);
        });

        rateInput.addEventListener('input', function() {
            rateInput.dataset.enteredRate = rateInput.value;
            calculateRow(row);
        });

        rateInput.addEventListener('focus', function() {
            if (gstTypeSelect.value === 'inclusive') {
                var enteredRate = parseFloat(rateInput.dataset.enteredRate) || parseFloat(rateInput.value) || 0;
                rateInput.value = enteredRate.toFixed(2);
            }
        });

        rateInput.addEventListener('blur', function() {
            convertRowInclusiveToExclusive(row);
        });

        gstTypeSelect.addEventListener('change', function() {
            convertRowInclusiveToExclusive(row);
        });

        taxSelect.addEventListener('change', function() {
            convertRowInclusiveToExclusive(row);
        });
    }

    function calculateRow(row) {
        var qtyInput = row.querySelector('.qty-input');
        var rateInput = row.querySelector('.rate-input');
        var gstTypeSelect = row.querySelector('.gst-type-select');
        var taxSelect = row.querySelector('.tax-select');
        var lineTotal = row.querySelector('.line-total');

        var qty = parseInt(qtyInput.value) || 0;
        var gstType = gstTypeSelect.value;
        var taxPct = parseFloat(taxSelect.value) || 0;
        var enteredRate = parseFloat(rateInput.dataset.enteredRate) || parseFloat(rateInput.value) || 0;

        var taxable = 0;
        var tax = 0;
        var net = 0;

        if (gstType === 'inclusive') {
            var rateExclTax = enteredRate / (1 + (taxPct / 100));
            taxable = qty * rateExclTax;
            tax = (taxable * taxPct) / 100;
            net = qty * enteredRate;
        } else {
            taxable = qty * enteredRate;
            tax = (taxable * taxPct) / 100;
            net = taxable + tax;
        }

        lineTotal.value = net.toFixed(2);
        calculateSummary();
    }

    // Initialize existing rows
    var existingRows = itemsContainer.querySelectorAll('.item-row');
    existingRows.forEach(function(row) {
        bindRowEvents(row);
        var rateInput = row.querySelector('.rate-input');
        if (rateInput) {
            rateInput.dataset.enteredRate = rateInput.value;
        }
    });

    // Summary calculations
    var summaryTaxable = document.getElementById('summary_taxable');
    var summaryCgst = document.getElementById('summary_cgst');
    var summarySgst = document.getElementById('summary_sgst');
    var summaryIgst = document.getElementById('summary_igst');
    var taxRegimeSelect = document.getElementById('tax_regime');
    var summaryRound = document.getElementById('summary_round');
    var summaryGrand = document.getElementById('summary_grand');
    var prevBalanceInput = document.getElementById('previous_balance');
    var receivedAmountInput = document.getElementById('received_amount');
    var summaryCurrentBalance = document.getElementById('summary_current_balance');

    function toggleRegimeFields() {
        var isIgst = taxRegimeSelect.value === 'igst';
        document.getElementById('summary_cgst_sgst_fields').classList.toggle('d-none', isIgst);
        document.getElementById('summary_cgst_sgst_fields2').classList.toggle('d-none', isIgst);
        document.getElementById('summary_igst_field').classList.toggle('d-none', !isIgst);
    }

    taxRegimeSelect.addEventListener('change', function() {
        toggleRegimeFields();
        calculateSummary();
    });

    function calculateSummary() {
        var taxableTotal = 0;
        var cgstTotal = 0;
        var sgstTotal = 0;
        var igstTotal = 0;
        var taxRegime = taxRegimeSelect.value;

        var rows = itemsContainer.querySelectorAll('.item-row');
        rows.forEach(function(row) {
            var qtyInput = row.querySelector('.qty-input');
            var rateInput = row.querySelector('.rate-input');
            var gstTypeSelect = row.querySelector('.gst-type-select');
            var taxSelect = row.querySelector('.tax-select');

            var qty = parseInt(qtyInput.value) || 0;
            var gstType = gstTypeSelect.value;
            var taxPct = parseFloat(taxSelect.value) || 0;
            var enteredRate = parseFloat(rateInput.dataset.enteredRate) || parseFloat(rateInput.value) || 0;

            var taxable = 0;
            var tax = 0;

            if (gstType === 'inclusive') {
                var rateExclTax = enteredRate / (1 + (taxPct / 100));
                taxable = qty * rateExclTax;
                tax = (taxable * taxPct) / 100;
            } else {
                taxable = qty * enteredRate;
                tax = (taxable * taxPct) / 100;
            }

            taxableTotal += taxable;
            if (taxRegime === 'igst') {
                igstTotal += tax;
            } else {
                cgstTotal += tax / 2;
                sgstTotal += tax / 2;
            }
        });

        var netTotalBeforeRound = taxableTotal + cgstTotal + sgstTotal + igstTotal;
        var netTotalRounded = Math.round(netTotalBeforeRound);
        var roundOff = netTotalRounded - netTotalBeforeRound;

        summaryTaxable.value = taxableTotal.toFixed(2);
        summaryCgst.value = cgstTotal.toFixed(2);
        summarySgst.value = sgstTotal.toFixed(2);
        summaryIgst.value = igstTotal.toFixed(2);
        summaryRound.value = roundOff.toFixed(2);
        summaryGrand.value = netTotalRounded.toFixed(2);

        var prev = parseFloat(prevBalanceInput.value) || 0;
        var rec = parseFloat(receivedAmountInput.value) || 0;
        var invoiceBal = netTotalRounded - rec;
        var curBal = prev + invoiceBal;

        summaryCurrentBalance.value = curBal.toFixed(2);
    }

    prevBalanceInput.addEventListener('input', calculateSummary);
    receivedAmountInput.addEventListener('input', calculateSummary);

    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        var gstSelects = document.querySelectorAll('.gst-type-select');
        gstSelects.forEach(function(select) {
            select.value = 'exclusive';
        });
    });

    // AJAX Quick Add Customer Form Handler
    var quickAddForm = document.getElementById('quickAddCustomerForm');
    var modalErrorAlert = document.getElementById('modalErrorAlert');
    var saveCustomerBtn = document.getElementById('btnSaveCustomer');
    
    quickAddForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveCustomerBtn.disabled = true;
        saveCustomerBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        modalErrorAlert.classList.add('d-none');
        
        var formData = new FormData(this);
        
        fetch('{{ route("admin.customers.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            saveCustomerBtn.disabled = false;
            saveCustomerBtn.innerHTML = 'Save Customer';
            
            if (res.status === 200 || res.status === 201) {
                var customer = res.body.customer;
                var fullName = customer.name;
                
                var option = document.createElement('option');
                option.value = customer.id;
                option.text = fullName + ' (' + customer.phone + ')';
                option.setAttribute('data-name', fullName);
                option.setAttribute('data-mobile', customer.phone);
                option.setAttribute('data-address', customer.address || '');
                option.setAttribute('data-gstin', customer.gstin || '');
                option.setAttribute('data-pan', customer.pan_no || '');
                
                customerSelect.appendChild(option);
                customerSelect.value = customer.id;
                $(customerSelect).trigger('change.select2');
                
                var event = new Event('change');
                customerSelect.dispatchEvent(event);
                
                var modalEl = document.getElementById('quickAddCustomerModal');
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalEl);
                }
                modalInstance.hide();
                
                quickAddForm.reset();
            } else {
                var errorMsg = 'Error saving customer.';
                if (res.body.errors) {
                    errorMsg = Object.values(res.body.errors).flat().join('<br>');
                } else if (res.body.message) {
                    errorMsg = res.body.message;
                }
                modalErrorAlert.innerHTML = errorMsg;
                modalErrorAlert.classList.remove('d-none');
            }
        })
        .catch(err => {
            saveCustomerBtn.disabled = false;
            saveCustomerBtn.innerHTML = 'Save Customer';
            modalErrorAlert.textContent = 'Server connection error.';
            modalErrorAlert.classList.remove('d-none');
            console.error(err);
        });
    });

    // Run initial calculation
    calculateSummary();
});
</script>
@endsection
