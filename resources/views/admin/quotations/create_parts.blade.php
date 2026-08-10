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
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Create Parts Quotation</h4>

    @if ($errors->has('items'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ $errors->first('items') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.quotations.store') }}" id="quotationForm">
                @csrf
                <input type="hidden" name="type" value="parts">
                
                <h5 class="card-title text-primary mb-3">Customer Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Select Customer (Existing)</label>
                        <div class="input-group">
                            <select id="customer_select" name="customer_id" class="form-select">
                                <option value="">-- New Customer / Walk-in --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
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
                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" id="customer_mobile" name="customer_mobile" class="form-control" value="{{ old('customer_mobile') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">GSTIN (Optional)</label>
                        <input type="text" id="customer_gstin" name="customer_gstin" class="form-control" value="{{ old('customer_gstin') }}" placeholder="15-digit GSTIN" maxlength="15">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PAN Number (Optional)</label>
                        <input type="text" id="customer_pan" name="customer_pan" class="form-control" value="{{ old('customer_pan') }}" placeholder="10-digit PAN" maxlength="10">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Place of Supply <span class="text-danger">*</span></label>
                        <input type="text" id="place_of_supply" name="place_of_supply" class="form-control" value="{{ old('place_of_supply', 'Rajasthan') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Billing Address</label>
                        <textarea id="customer_address" name="customer_address" class="form-control" rows="2">{{ old('customer_address') }}</textarea>
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
                                <i class="bx bx-show me-1"></i> View Complete Ledger / History
                            </a>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Total Amount Invoiced</small>
                                    <span id="lbl_ledger_total" class="h6 mb-0 text-dark fw-bold">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Total Amount Paid/Deposited</small>
                                    <span id="lbl_ledger_paid" class="h6 mb-0 text-success fw-bold">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Current Outstanding Balance</small>
                                    <span id="lbl_ledger_outstanding" class="h6 mb-0 text-danger fw-bold">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="card-title text-primary mb-3">Quotation Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Regime <span class="text-danger">*</span></label>
                        <select name="tax_regime" id="tax_regime" class="form-select no-select2" required>
                            <option value="cgst_sgst" {{ old('tax_regime') === 'cgst_sgst' ? 'selected' : '' }}>CGST + SGST</option>
                            <option value="igst" {{ old('tax_regime') === 'igst' ? 'selected' : '' }}>IGST</option>
                        </select>
                    </div>
                </div>

                <h5 class="card-title text-primary mb-3">Select Parts & Items</h5>
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
                            <tr id="noItemsNotice">
                                <td colspan="8" class="text-center p-4 text-muted bg-light">
                                    <i class="bx bx-package fs-2 mb-2 d-block text-primary"></i>
                                    No items added yet. Click <strong>"Search & Add Item (Modal)"</strong> below to select spare parts.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4 d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btnOpenSearchModal">
                        <i class="bx bx-search me-1"></i> Search & Add Item (Modal)
                    </button>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 offset-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3 text-secondary">Summary & Calculations</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td>Taxable Amount:</td>
                                        <td class="text-end fw-bold">₹<span id="summary_taxable">0.00</span></td>
                                    </tr>
                                    <tr class="cgst-summary">
                                        <td>CGST Amount:</td>
                                        <td class="text-end fw-bold">₹<span id="summary_cgst">0.00</span></td>
                                    </tr>
                                    <tr class="sgst-summary">
                                        <td>SGST Amount:</td>
                                        <td class="text-end fw-bold">₹<span id="summary_sgst">0.00</span></td>
                                    </tr>
                                    <tr class="igst-summary d-none">
                                        <td>IGST Amount:</td>
                                        <td class="text-end fw-bold">₹<span id="summary_igst">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Round Off:</td>
                                        <td class="text-end fw-bold">₹<span id="summary_round_off">0.00</span></td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fs-5 fw-bold text-primary">Grand Total:</td>
                                        <td class="text-end fs-5 fw-bold text-primary">₹<span id="summary_grand_total">0.00</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Remarks / Notes</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="quickAddCustomerForm">
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

<!-- Item Search & Add/Edit Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="itemModalTitle"><i class="bx bx-package me-2"></i>Select Spare Parts</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Search Box -->
                <div class="row g-3 mb-3 align-items-center" id="modalSearchContainer">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-search fs-5"></i></span>
                            <input type="text" id="modalPartSearch" class="form-control form-control-lg" placeholder="Search by Part Name or Part Number...">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-label-primary p-2 fs-6" id="modalPartsCount">Showing {{ count($spareParts) }} parts</span>
                    </div>
                </div>

                <!-- Single Item Edit Panel (visible only in Edit Mode) -->
                <div id="modalEditPanel" class="d-none alert alert-info mb-3">
                    <h6 class="fw-bold mb-3"><i class="bx bx-edit me-1"></i> Edit Selected Item</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Spare Part</label>
                            <input type="text" id="editPartName" class="form-control bg-white fw-bold" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Stock Available</label>
                            <input type="text" id="editPartStock" class="form-control bg-white text-center fw-bold" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="editQty" class="form-control text-center fw-bold" min="1" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rate (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="editRate" class="form-control fw-bold" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">GST Type <span class="text-danger">*</span></label>
                            <select id="editGstType" class="form-select no-select2">
                                <option value="exclusive">Exclusive</option>
                                <option value="inclusive">Inclusive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">GST % <span class="text-danger">*</span></label>
                            <select id="editTaxPct" class="form-select no-select2">
                                <option value="0.00">0%</option>
                                <option value="5.00">5%</option>
                                <option value="12.00">12%</option>
                                <option value="18.00">18%</option>
                                <option value="28.00">28%</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Serial No. / Warranty Notes</label>
                            <input type="text" id="editNotes" class="form-control" placeholder="Serial No. / Warranty Notes (Optional)">
                        </div>
                    </div>
                </div>

                <!-- Spare Parts Table (visible in Search/Add Mode) -->
                <div class="table-responsive" id="modalTableWrapper" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle" id="modalPartsTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th style="width: 5%; text-align: center;">
                                    <input type="checkbox" id="selectAllModalParts" class="form-check-input" title="Select All">
                                </th>
                                <th style="width: 40%;">Part Number & Name</th>
                                <th style="width: 18%; text-align: center;">Stock Available</th>
                                <th style="width: 22%;">Rate / Selling Price (INR)</th>
                                <th style="width: 15%; text-align: center;">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="modalPartsBody">
                            @foreach($spareParts as $p)
                            <tr class="modal-part-row" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name) }}" data-partno="{{ strtolower($p->part_no) }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input modal-part-checkbox" 
                                           data-id="{{ $p->id }}"
                                           data-name="{{ $p->part_no ? $p->part_no . ' - ' : '' }}{{ $p->name }}"
                                           data-price="{{ number_format($p->selling_price, 2, '.', '') }}"
                                           data-stock="{{ $p->qty_available }}">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $p->name }}</div>
                                    <small class="text-muted"><i class="bx bx-purchase-tag me-1"></i>Part No: <strong>{{ $p->part_no ?: 'N/A' }}</strong></small>
                                </td>
                                <td class="text-center">
                                    @if($p->qty_available > 0)
                                        <span class="badge bg-label-success fs-6">{{ $p->qty_available }}</span>
                                    @else
                                        <span class="badge bg-label-danger fs-6">Out of Stock (0)</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm modal-part-rate" value="{{ number_format($p->selling_price, 2, '.', '') }}" min="0">
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center modal-part-qty" value="1" min="1">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="text-muted fw-bold" id="selectedPartsCount">0 parts selected</span>
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="btnAddSelectedModalParts"><i class="bx bx-plus me-1"></i> Add Selected Items to Quotation</button>
                    <button type="button" class="btn btn-primary d-none" id="btnUpdateModalItem"><i class="bx bx-check me-1"></i> Save Changes</button>
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
        } else {
            customerNameInput.value = '';
            customerMobileInput.value = '';
            customerAddressInput.value = '';
            customerGstInput.value = '';
            customerPanInput.value = '';
        }
        fetchCustomerLedgerSummary();
    });

    function fetchCustomerLedgerSummary() {
        var customerId = $('#customer_select').val();
        var cName = $.trim($('#customer_name').val());
        var cMobile = $.trim($('#customer_mobile').val());

        if (!customerId && cName.length < 2 && cMobile.length < 3) {
            $('#customer_ledger_card').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('admin.customers.ledger-summary') }}",
            type: 'GET',
            data: {
                customer_id: customerId,
                customer_name: cName,
                customer_mobile: cMobile
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

    var itemsContainer = document.getElementById('itemsContainer');
    var btnOpenSearchModal = document.getElementById('btnOpenSearchModal');
    var itemIndex = 1;
    var editingTargetRow = null;

    var itemModalEl = document.getElementById('addItemModal');
    var itemModal = new bootstrap.Modal(itemModalEl);
    var itemModalTitle = document.getElementById('itemModalTitle');
    var modalSearchContainer = document.getElementById('modalSearchContainer');
    var modalTableWrapper = document.getElementById('modalTableWrapper');
    var modalEditPanel = document.getElementById('modalEditPanel');
    var btnUpdateModalItem = document.getElementById('btnUpdateModalItem');
    var btnAddSelectedModalParts = document.getElementById('btnAddSelectedModalParts');
    var selectAllModalParts = document.getElementById('selectAllModalParts');
    var selectedPartsCount = document.getElementById('selectedPartsCount');
    var modalPartSearch = document.getElementById('modalPartSearch');
    var modalPartsCount = document.getElementById('modalPartsCount');

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

    // Create New Row HTML Helper
    function createRow(partId = '', partName = '', qty = 1, rate = 0.00, gstType = 'exclusive', taxPct = '18.00', notes = '', stock = 0) {
        var row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td>
                <input type="hidden" name="items[${itemIndex}][spare_part_id]" class="part-id-input" value="${partId}" required>
                <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="${partName}" placeholder="Click 'Search & Add Item' to select part" required>
                <div class="mt-2">
                    <input type="text" name="items[${itemIndex}][serial_no_warranty_notes]" class="form-control form-control-sm notes-input" placeholder="Serial No. / Warranty Notes (Optional)" value="${notes}">
                </div>
            </td>
            <td class="text-center bg-light">
                <span class="stock-badge fw-bold ${stock > 0 ? 'text-success' : 'text-secondary'}">${stock}</span>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input text-center" min="1" value="${qty}" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control rate-input" min="0" value="${parseFloat(rate).toFixed(2)}" data-entered-rate="${parseFloat(rate).toFixed(2)}" required>
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
                <input type="text" class="form-control line-total bg-transparent border-0 fw-bold" readonly value="0.00">
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

    // Open Search Modal in Add Mode
    btnOpenSearchModal.addEventListener('click', function() {
        editingTargetRow = null;
        itemModalTitle.innerHTML = '<i class="bx bx-package me-2"></i>Select Spare Parts';
        modalSearchContainer.classList.remove('d-none');
        modalTableWrapper.classList.remove('d-none');
        modalEditPanel.classList.add('d-none');
        btnUpdateModalItem.classList.add('d-none');
        if (btnAddSelectedModalParts) btnAddSelectedModalParts.classList.remove('d-none');
        if (selectedPartsCount) selectedPartsCount.classList.remove('d-none');
        
        // Reset checkboxes
        document.querySelectorAll('.modal-part-checkbox').forEach(function(cb) { cb.checked = false; });
        if (selectAllModalParts) selectAllModalParts.checked = false;
        updateSelectedPartsCount();

        modalPartSearch.value = '';
        filterModalParts();
        itemModal.show();
        setTimeout(function() { modalPartSearch.focus(); }, 400);
    });

    // Modal Live Search Filter
    function filterModalParts() {
        var query = modalPartSearch.value.trim().toLowerCase();
        var rows = document.querySelectorAll('.modal-part-row');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var partNo = row.getAttribute('data-partno') || '';
            if (!query || name.includes(query) || partNo.includes(query)) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        modalPartsCount.textContent = 'Showing ' + visibleCount + ' parts';
    }

    modalPartSearch.addEventListener('input', filterModalParts);

    // Checkbox selection handlers
    if (selectAllModalParts) {
        selectAllModalParts.addEventListener('change', function() {
            var isChecked = this.checked;
            var visibleCheckboxes = document.querySelectorAll('.modal-part-row:not(.d-none) .modal-part-checkbox');
            visibleCheckboxes.forEach(function(cb) {
                cb.checked = isChecked;
            });
            updateSelectedPartsCount();
        });
    }

    document.getElementById('modalPartsBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('modal-part-checkbox')) {
            updateSelectedPartsCount();
        }
    });

    function updateSelectedPartsCount() {
        var checked = document.querySelectorAll('.modal-part-checkbox:checked');
        if (selectedPartsCount) {
            selectedPartsCount.textContent = checked.length + ' part(s) selected';
        }
    }

    // Add Selected Parts from Modal to Main Table
    if (btnAddSelectedModalParts) {
        btnAddSelectedModalParts.addEventListener('click', function() {
            var checkedBoxes = document.querySelectorAll('.modal-part-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one part using the checkboxes.');
                return;
            }

            checkedBoxes.forEach(function(cb) {
                var row = cb.closest('.modal-part-row');
                var partId = cb.getAttribute('data-id');
                var partName = cb.getAttribute('data-name');
                var stock = parseInt(cb.getAttribute('data-stock')) || 0;
                var qtyInput = row.querySelector('.modal-part-qty');
                var rateInput = row.querySelector('.modal-part-rate');
                var qty = parseInt(qtyInput.value) || 1;
                var rate = parseFloat(rateInput.value) || parseFloat(cb.getAttribute('data-price')) || 0;

                // Check if there is an unselected first row in the table
                var existingRows = itemsContainer.querySelectorAll('.item-row');
                var targetRow = null;

                if (existingRows.length === 1) {
                    var firstPartId = existingRows[0].querySelector('.part-id-input');
                    if (!firstPartId.value) {
                        targetRow = existingRows[0];
                    }
                }

                if (targetRow) {
                    targetRow.querySelector('.part-id-input').value = partId;
                    targetRow.querySelector('.part-name-input').value = partName;
                    var stockBadge = targetRow.querySelector('.stock-badge');
                    stockBadge.textContent = stock;
                    stockBadge.className = 'stock-badge fw-bold ' + (stock > 0 ? 'text-success' : 'text-secondary');

                    var qtyIn = targetRow.querySelector('.qty-input');
                    var rateIn = targetRow.querySelector('.rate-input');
                    qtyIn.value = qty;
                    rateIn.value = rate.toFixed(2);
                    rateIn.dataset.enteredRate = rate.toFixed(2);

                    calculateRow(targetRow);
                } else {
                    var newRow = createRow(partId, partName, qty, rate, 'exclusive', '18.00', '', stock);
                    calculateRow(newRow);
                }

                cb.checked = false;
            });

            if (selectAllModalParts) selectAllModalParts.checked = false;
            updateSelectedPartsCount();
            checkNoItemsNotice();
            calculateSummary();
            itemModal.hide();
        });
    }

    // Remove Row Event Delegation
    itemsContainer.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.btn-remove-row');
        if (removeBtn) {
            var row = removeBtn.closest('.item-row');
            row.remove();
            checkNoItemsNotice();
            calculateSummary();
            return;
        }

        var editBtn = e.target.closest('.btn-edit-row');
        if (editBtn) {
            editingTargetRow = editBtn.closest('.item-row');
            openModalForEdit(editingTargetRow);
        }
    });

    // Open Modal in Edit Mode
    function openModalForEdit(row) {
        var partId = row.querySelector('.part-id-input').value;
        var partName = row.querySelector('.part-name-input').value;
        
        if (!partId) {
            alert('Please select a spare part first before editing.');
            return;
        }

        var stock = row.querySelector('.stock-badge').textContent || '0';
        var qty = row.querySelector('.qty-input').value || 1;
        var rateInput = row.querySelector('.rate-input');
        var enteredRate = rateInput.dataset.enteredRate || rateInput.value || 0;
        var gstType = row.querySelector('.gst-type-select').value;
        var taxPct = row.querySelector('.tax-select').value;
        var notesInput = row.querySelector('.notes-input');
        var notes = notesInput ? notesInput.value : '';

        document.getElementById('editPartName').value = partName;
        document.getElementById('editPartStock').value = stock;
        document.getElementById('editQty').value = qty;
        document.getElementById('editRate').value = parseFloat(enteredRate).toFixed(2);
        document.getElementById('editGstType').value = gstType;
        document.getElementById('editTaxPct').value = taxPct;
        document.getElementById('editNotes').value = notes;

        itemModalTitle.innerHTML = '<i class="bx bx-edit me-2"></i>Edit Quotation Item';
        modalSearchContainer.classList.add('d-none');
        modalTableWrapper.classList.add('d-none');
        modalEditPanel.classList.remove('d-none');
        btnUpdateModalItem.classList.remove('d-none');
        if (btnAddSelectedModalParts) btnAddSelectedModalParts.classList.add('d-none');
        if (selectedPartsCount) selectedPartsCount.classList.add('d-none');

        itemModal.show();
    }

    // Save Changes from Edit Modal
    btnUpdateModalItem.addEventListener('click', function() {
        if (!editingTargetRow) return;

        var newQty = parseInt(document.getElementById('editQty').value) || 1;
        var newRate = parseFloat(document.getElementById('editRate').value) || 0;
        var newGstType = document.getElementById('editGstType').value;
        var newTaxPct = document.getElementById('editTaxPct').value;
        var newNotes = document.getElementById('editNotes').value;

        var qtyInput = editingTargetRow.querySelector('.qty-input');
        var rateInput = editingTargetRow.querySelector('.rate-input');
        var gstTypeSelect = editingTargetRow.querySelector('.gst-type-select');
        var taxSelect = editingTargetRow.querySelector('.tax-select');
        var notesInput = editingTargetRow.querySelector('.notes-input');

        qtyInput.value = newQty;
        rateInput.dataset.enteredRate = newRate.toFixed(2);
        rateInput.value = newRate.toFixed(2);
        gstTypeSelect.value = newGstType;
        taxSelect.value = newTaxPct;

        if (notesInput) {
            notesInput.value = newNotes;
        }

        convertRowInclusiveToExclusive(editingTargetRow);
        calculateRow(editingTargetRow);
        calculateSummary();

        itemModal.hide();
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

        if (lineTotal) {
            lineTotal.value = '₹' + net.toFixed(2);
        }
        calculateSummary();
    }

    // Summary calculations
    var summaryTaxable = document.getElementById('summary_taxable');
    var summaryCgst = document.getElementById('summary_cgst');
    var summarySgst = document.getElementById('summary_sgst');
    var summaryIgst = document.getElementById('summary_igst');
    var taxRegimeSelect = document.getElementById('tax_regime');
    var summaryRoundOff = document.getElementById('summary_round_off');
    var summaryGrandTotal = document.getElementById('summary_grand_total');

    function toggleRegimeFields() {
        var isIgst = taxRegimeSelect.value === 'igst';
        document.querySelectorAll('.cgst-summary, .sgst-summary').forEach(function(el) {
            el.classList.toggle('d-none', isIgst);
        });
        var igstEl = document.querySelector('.igst-summary');
        if (igstEl) igstEl.classList.toggle('d-none', !isIgst);
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

            if (!qtyInput || !rateInput) return;

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

        if (summaryTaxable) summaryTaxable.textContent = taxableTotal.toFixed(2);
        if (summaryCgst) summaryCgst.textContent = cgstTotal.toFixed(2);
        if (summarySgst) summarySgst.textContent = sgstTotal.toFixed(2);
        if (summaryIgst) summaryIgst.textContent = igstTotal.toFixed(2);
        if (summaryRoundOff) summaryRoundOff.textContent = roundOff.toFixed(2);
        if (summaryGrandTotal) summaryGrandTotal.textContent = netTotalRounded.toFixed(2);
    }

    document.getElementById('quotationForm').addEventListener('submit', function(e) {
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
    
    if (quickAddForm) {
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
                    $(customerSelect).trigger('change');
                    
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
    }
});
</script>
@endsection
