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
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Purchase Orders /</span> Edit
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Edit Purchase Order: {{ $purchaseOrder->order_number }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" id="poForm">
                @csrf @method('PUT')
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', $purchaseOrder->order_date?->format('Y-m-d')) }}" required>
                        @error('order_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Expected Date</label>
                        <input type="date" name="expected_date" class="form-control @error('expected_date') is-invalid @enderror" value="{{ old('expected_date', $purchaseOrder->expected_date?->format('Y-m-d')) }}">
                        @error('expected_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Order notes or reference details...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Auto-Appearing Supplier Ledger Card -->
                <div id="supplier_ledger_card" class="card mb-4 border border-info-subtle shadow-sm d-none" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 text-info fw-bold">
                                <i class="bx bx-book-content me-1"></i> Supplier Ledger Summary (<span id="ledger_supplier_name">Supplier</span>)
                            </h6>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Total Orders Amount</small>
                                    <span id="lbl_supplier_total" class="h6 mb-0 text-dark fw-bold">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Total Amount Paid/Deposited</small>
                                    <span id="lbl_supplier_paid" class="h6 mb-0 text-success fw-bold">₹0.00</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded shadow-xs border">
                                    <small class="text-muted d-block text-uppercase fw-semibold">Current Outstanding Balance</small>
                                    <span id="lbl_supplier_outstanding" class="h6 mb-0 text-danger fw-bold">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="card-title text-primary mb-3">Order Items (Parts)</h5>
                @error('items') <div class="alert alert-danger py-2 mb-3">{{ $message }}</div> @enderror

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead>
                            <tr class="table-dark">
                                <th style="width: 40%;">Part Name / Number <span class="text-danger">*</span></th>
                                <th style="width: 15%; text-align: center;">Stock Available</th>
                                <th style="width: 12%; text-align: center;">Qty <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Unit Price (INR) <span class="text-danger">*</span></th>
                                <th style="width: 13%;">Total Amount (INR)</th>
                                <th style="width: 5%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <tr id="noItemsNotice" class="{{ count($purchaseOrder->items) > 0 ? 'd-none' : '' }}">
                                <td colspan="6" class="text-center p-4 text-muted bg-light">
                                    <i class="bx bx-package fs-2 mb-2 d-block text-primary"></i>
                                    No items added yet. Click <strong>"Search & Add Item (Modal)"</strong> below to select spare parts.
                                </td>
                            </tr>
                            @foreach($purchaseOrder->items as $i => $item)
                            @php
                                $stockVal = $item->sparePart?->qty_available ?? 0;
                            @endphp
                            <tr class="item-row">
                                <td>
                                    <input type="hidden" name="items[{{ $i }}][spare_part_id]" class="part-id-input" value="{{ $item->spare_part_id }}" required>
                                    <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="{{ $item->sparePart ? $item->sparePart->part_no . ' - ' . $item->sparePart->name : '' }}" placeholder="Click 'Search & Add Item' to select part" required>
                                </td>
                                <td class="text-center bg-light">
                                    <span class="stock-badge fw-bold {{ $stockVal > 0 ? 'text-success' : 'text-secondary' }}">{{ $stockVal }}</span>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control qty text-center" min="1" value="{{ $item->quantity }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control unit-price" min="0" value="{{ number_format($item->unit_price, 2, '.', '') }}" required>
                                </td>
                                <td class="bg-light">
                                    <input type="text" class="form-control line-total bg-transparent border-0 fw-bold" readonly value="{{ number_format($item->total_price, 2, '.', '') }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4 d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btnOpenSearchModal">
                        <i class="bx bx-search me-1"></i> Search & Add Item (Modal)
                    </button>
                </div>

                <div class="card mb-4 bg-light border border-light-subtle">
                    <div class="card-body text-end py-3">
                        <h4 class="mb-0">Grand Total: ₹ <span id="grandTotal" class="text-primary fw-bold">{{ number_format($purchaseOrder->total_amount, 2, '.', '') }}</span></h4>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Order</button>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary me-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Search & Add Modal -->
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

                <!-- Spare Parts Table -->
                <div class="table-responsive" id="modalTableWrapper" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle" id="modalPartsTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th style="width: 5%; text-align: center;">
                                    <input type="checkbox" id="selectAllModalParts" class="form-check-input" title="Select All">
                                </th>
                                <th style="width: 45%;">Part Number & Name</th>
                                <th style="width: 18%; text-align: center;">Stock Available</th>
                                <th style="width: 17%;">Purchase Price (INR)</th>
                                <th style="width: 15%; text-align: center;">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="modalPartsBody">
                            @foreach($spareParts as $p)
                            <tr class="modal-part-row" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name) }}" data-partno="{{ strtolower($p->part_no) }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input modal-part-checkbox" 
                                           data-id="{{ $p->id }}"
                                           data-name="{{ $p->part_no }} - {{ $p->name }}"
                                           data-price="{{ number_format($p->purchase_price, 2, '.', '') }}"
                                           data-stock="{{ $p->qty_available }}">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $p->name }}</div>
                                    <small class="text-muted"><i class="bx bx-purchase-tag me-1"></i>Part No: <strong>{{ $p->part_no }}</strong></small>
                                </td>
                                <td class="text-center">
                                    @if($p->qty_available > 0)
                                        <span class="badge bg-label-success fs-6">{{ $p->qty_available }}</span>
                                    @else
                                        <span class="badge bg-label-secondary fs-6">Stock: 0</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm modal-part-rate" value="{{ number_format($p->purchase_price, 2, '.', '') }}" min="0">
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
                <span class="text-muted fw-bold" id="selectedPartsCount">0 part(s) selected</span>
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="btnAddSelectedModalParts"><i class="bx bx-plus me-1"></i> Add Selected Items to Order</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var itemsContainer = document.getElementById('itemsContainer');
    var btnAddRow = document.getElementById('btnAddRow');
    var btnOpenSearchModal = document.getElementById('btnOpenSearchModal');
    var itemIndex = {{ count($purchaseOrder->items) }};

    var itemModalEl = document.getElementById('addItemModal');
    var itemModal = new bootstrap.Modal(itemModalEl);
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

    function createRow(partId = '', partName = '', qty = 1, unitPrice = 0.00, stock = 0) {
        var row = document.createElement('tr');
        row.className = 'item-row';

        row.innerHTML = `
            <td>
                <input type="hidden" name="items[${itemIndex}][spare_part_id]" class="part-id-input" value="${partId}" required>
                <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="${partName}" placeholder="Click 'Search & Add Item' to select part" required>
            </td>
            <td class="text-center bg-light">
                <span class="stock-badge fw-bold ${stock > 0 ? 'text-success' : 'text-secondary'}">${stock}</span>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty text-center" min="1" value="${qty}" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control unit-price" min="0" value="${parseFloat(unitPrice).toFixed(2)}" required>
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
        calculateRow(row);
        checkNoItemsNotice();
        return row;
    }

    btnOpenSearchModal.addEventListener('click', function() {
        document.querySelectorAll('.modal-part-checkbox').forEach(function(cb) { cb.checked = false; });
        if (selectAllModalParts) selectAllModalParts.checked = false;
        updateSelectedPartsCount();
        modalPartSearch.value = '';
        filterModalParts();
        itemModal.show();
        setTimeout(function() { modalPartSearch.focus(); }, 400);
    });

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
                var rate = (rateInput && rateInput.value !== '' && !isNaN(parseFloat(rateInput.value))) ? parseFloat(rateInput.value) : (parseFloat(cb.getAttribute('data-price')) || 0);

                var existingRows = itemsContainer.querySelectorAll('.item-row');
                var targetRow = null;

                existingRows.forEach(function(r) {
                    var pidInput = r.querySelector('.part-id-input');
                    if (pidInput && pidInput.value == partId) {
                        targetRow = r;
                    }
                });

                if (targetRow) {
                    var qtyIn = targetRow.querySelector('.qty');
                    var rateIn = targetRow.querySelector('.unit-price');
                    qtyIn.value = qty;
                    rateIn.value = rate.toFixed(2);
                    var stockBadge = targetRow.querySelector('.stock-badge');
                    if (stockBadge) {
                        stockBadge.textContent = stock;
                        stockBadge.className = 'stock-badge fw-bold ' + (stock > 0 ? 'text-success' : 'text-secondary');
                    }
                    calculateRow(targetRow);
                } else {
                    createRow(partId, partName, qty, rate, stock);
                }

                cb.checked = false;
            });

            if (selectAllModalParts) selectAllModalParts.checked = false;
            updateSelectedPartsCount();
            itemModal.hide();
        });
    }

    function bindRowEvents(row) {
        var qtyInput = row.querySelector('.qty');
        var priceInput = row.querySelector('.unit-price');
        var removeBtn = row.querySelector('.btn-remove-row');

        if (qtyInput) {
            qtyInput.addEventListener('input', function() { calculateRow(row); });
        }

        if (priceInput) {
            priceInput.addEventListener('input', function() { calculateRow(row); });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                row.remove();
                calcGrandTotal();
                checkNoItemsNotice();
            });
        }
    }

    function calculateRow(row) {
        var qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        var unitPrice = parseFloat(row.querySelector('.unit-price')?.value) || 0;
        var total = qty * unitPrice;
        var lineTotal = row.querySelector('.line-total');
        if (lineTotal) {
            lineTotal.value = total.toFixed(2);
        }
        calcGrandTotal();
    }

    function calcGrandTotal() {
        var grandTotal = 0;
        var lineTotals = itemsContainer.querySelectorAll('.line-total');
        lineTotals.forEach(function(lt) {
            grandTotal += parseFloat(lt.value) || 0;
        });
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }

    // Bind events to existing rows
    document.querySelectorAll('.item-row').forEach(function(row) {
        bindRowEvents(row);
        calculateRow(row);
    });
    calcGrandTotal();
    checkNoItemsNotice();

    function fetchSupplierLedgerSummary() {
        var supplierId = $('select[name="supplier_id"]').val();
        if (!supplierId) {
            $('#supplier_ledger_card').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('admin.suppliers.ledger-summary') }}",
            type: 'GET',
            data: { supplier_id: supplierId },
            success: function(resp) {
                if (resp.success) {
                    $('#ledger_supplier_name').text(resp.supplier_name);
                    $('#lbl_supplier_total').text('₹' + parseFloat(resp.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#lbl_supplier_paid').text('₹' + parseFloat(resp.paid_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    var bal = parseFloat(resp.outstanding_balance);
                    $('#lbl_supplier_outstanding').text('₹' + bal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    if (bal > 0) {
                        $('#lbl_supplier_outstanding').removeClass('text-success text-muted').addClass('text-danger');
                    } else {
                        $('#lbl_supplier_outstanding').removeClass('text-danger').addClass('text-success');
                    }

                    $('#supplier_ledger_card').removeClass('d-none');
                } else {
                    $('#supplier_ledger_card').addClass('d-none');
                }
            },
            error: function() {
                $('#supplier_ledger_card').addClass('d-none');
            }
        });
    }

    $(document).on('change', 'select[name="supplier_id"]', function(){
        fetchSupplierLedgerSummary();
    });
    fetchSupplierLedgerSummary();
});

document.getElementById('poForm').addEventListener('submit', function(e) {
    var valid = true;
    var items = document.querySelectorAll('.item-row');
    if (items.length === 0) {
        alert('Please add at least one item using "Search & Add Item".');
        e.preventDefault();
        return;
    }
    items.forEach(function(row) {
        var partIdInput = row.querySelector('.part-id-input');
        var qtyInput = row.querySelector('.qty');
        if (partIdInput && !partIdInput.value) {
            valid = false;
        }
        if (qtyInput && (parseInt(qtyInput.value) || 0) < 1) {
            valid = false;
            qtyInput.classList.add('is-invalid');
        } else if (qtyInput) {
            qtyInput.classList.remove('is-invalid');
        }
    });
    if (!valid) {
        e.preventDefault();
        alert('Please fill in all required fields for each item.');
    }
});
</script>
@endsection
