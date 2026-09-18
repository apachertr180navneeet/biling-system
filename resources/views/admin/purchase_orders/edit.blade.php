@extends('admin.layouts.app')



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
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $i }}][spare_part_id]" class="part-id-input" value="{{ $item->spare_part_id }}" required>
                                    <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="{{ $item->sparePart ? $item->sparePart->part_no . ' - ' . $item->sparePart->name : '' }}" placeholder="Click 'Search & Add Item' to select part" required>
                                    @if(($item->received_quantity ?? 0) > 0)
                                    <small class="text-success fw-bold d-block mt-1"><i class="bx bx-check-circle me-1"></i>Received: {{ $item->received_quantity }}</small>
                                    @endif
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
<div class="modal fade spare-parts-modal" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1050px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">
                    <i class="bx bx-package me-2" style="color: #a5b4fc; font-size: 1.4rem;"></i>Select Spare Parts
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search & Notice Row -->
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
                                data-hsn="{{ strtolower($p->hsn_sac_code ?? '') }}"
                                data-displayname="{{ $p->part_no }} - {{ $p->name }}"
                                data-price="{{ number_format($p->purchase_price, 2, '.', '') }}"
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
                                    <input type="number" step="0.01" class="form-control form-control-sm text-center modal-part-rate fw-bold" value="{{ number_format($p->purchase_price, 2, '.', '') }}" min="0" style="max-width: 100px; margin: 0 auto; border-radius: 6px;">
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
                        <i class="bx bx-plus me-1"></i> Add Selected Items to Order
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
    var itemsContainer = document.getElementById('itemsContainer');
    var btnAddRow = document.getElementById('btnAddRow');
    var btnOpenSearchModal = document.getElementById('btnOpenSearchModal');
    var itemIndex = {{ count($purchaseOrder->items) }};

    var itemModalEl = document.getElementById('addItemModal');
    var itemModal = new bootstrap.Modal(itemModalEl);
    var btnAddSelectedModalParts = document.getElementById('btnAddSelectedModalParts');
    var checkAllParts = document.getElementById('checkAllParts');
    var selectedPartsCount = document.getElementById('selectedPartsCount');
    var modalPartSearch = document.getElementById('modalPartSearch');

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
    });

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

    // Select All Checkbox
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
            var qty = parseInt(e.target.value) || 0;
            if (cb) {
                cb.checked = (qty > 0);
                if (cb.checked) {
                    row.classList.add('row-selected');
                } else {
                    row.classList.remove('row-selected');
                }
            }
            updateSelectedPartsCount();
        }
    });

    function updateSelectedPartsCount() {
        var count = document.querySelectorAll('.modal-part-checkbox:checked').length;
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
                if ((cb && cb.checked) || (qtyInput && parseInt(qtyInput.value) > 0 && cb && cb.checked)) {
                    selectedRows.push(row);
                }
            });

            if (selectedRows.length === 0) {
                alert('Please select at least one spare part from the list.');
                return;
            }

            selectedRows.forEach(function(row) {
                var partId = row.getAttribute('data-id');
                var partName = row.getAttribute('data-displayname') || row.getAttribute('data-name');
                var stock = parseInt(row.getAttribute('data-stock')) || 0;
                var qtyInput = row.querySelector('.modal-part-qty');
                var rateInput = row.querySelector('.modal-part-rate');
                var qty = parseInt(qtyInput.value) || 1;
                var rate = (rateInput && rateInput.value !== '' && !isNaN(parseFloat(rateInput.value))) ? parseFloat(rateInput.value) : (parseFloat(row.getAttribute('data-price')) || 0);

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

                var cb = row.querySelector('.modal-part-checkbox');
                if (cb) cb.checked = false;
                row.classList.remove('row-selected');
            });

            if (checkAllParts) checkAllParts.checked = false;
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
