@extends('admin.layouts.app')
@section('style')
<style>
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }

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
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Purchase Orders /</span> Edit
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Edit Purchase Order: {{ $purchaseOrder->order_number }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" id="poForm">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Order Date</label>
                    <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', $purchaseOrder->order_date->format('Y-m-d')) }}">
                    @error('order_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Expected Date</label>
                    <input type="date" name="expected_date" class="form-control @error('expected_date') is-invalid @enderror" value="{{ old('expected_date', $purchaseOrder->expected_date?->format('Y-m-d')) }}">
                    @error('expected_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Order Items</h5>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" id="btnOpenSearchModal">
                            <i class="bx bx-search me-1"></i> Search & Add Item (Modal)
                        </button>
                    </div>
                </div>
                @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div id="itemsContainer">
                    @foreach($purchaseOrder->items as $i => $item)
                    <div class="item-row row mb-2 align-items-center">
                        <div class="col-md-5">
                            <input type="hidden" name="items[{{ $i }}][spare_part_id]" class="part-id-input" value="{{ $item->spare_part_id }}" required>
                            <input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="{{ $item->sparePart->part_no ?? '' }} - {{ $item->sparePart->name ?? '' }} (Stock: {{ $item->sparePart->qty_available ?? 0 }})" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $i }}][quantity]" class="form-control qty text-center" min="1" value="{{ $item->quantity }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control unit-price" min="0" value="{{ number_format($item->unit_price, 2, '.', '') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control line-total" readonly value="{{ number_format($item->total_price, 2, '.', '') }}">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-item me-1" title="Edit via Modal"><i class="bx bx-edit"></i></button>
                            <button type="button" class="btn btn-sm btn-danger remove-item" title="Remove">X</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr>
                <div class="text-end">
                    <h5>Total: <span id="grandTotal">{{ number_format($purchaseOrder->total_amount, 2, '.', '') }}</span></h5>
                </div>
                <button type="submit" class="btn btn-primary">Update Order</button>
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<!-- Item Search & Add/Edit Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="itemModalTitle"><i class="bx bx-package me-2"></i>Select Spare Parts for Purchase</h5>
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

                <!-- Single Item Edit Panel (visible in Edit Mode) -->
                <div id="modalEditPanel" class="d-none alert alert-info mb-3">
                    <h6 class="fw-bold mb-3"><i class="bx bx-edit me-1"></i> Edit Purchase Order Item</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Spare Part</label>
                            <input type="text" id="editPartName" class="form-control bg-white fw-bold" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="editQty" class="form-control text-center fw-bold" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Unit Purchase Price (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="editUnitPrice" class="form-control fw-bold" min="0">
                        </div>
                    </div>
                </div>

                <!-- Spare Parts Table (visible in Search/Add Mode) -->
                <div class="table-responsive" id="modalTableWrapper" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle" id="modalPartsTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th style="width: 40%;">Part Number & Name</th>
                                <th style="width: 15%; text-align: center;">Current Stock</th>
                                <th style="width: 20%;">Purchase Price (INR)</th>
                                <th style="width: 10%; text-align: center;">Qty</th>
                                <th style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="modalPartsBody">
                            @foreach($spareParts as $part)
                            <tr class="modal-part-row" data-id="{{ $part->id }}" data-name="{{ strtolower($part->name) }}" data-partno="{{ strtolower($part->part_no) }}">
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $part->name }}</div>
                                    <small class="text-muted"><i class="bx bx-purchase-tag me-1"></i>Part No: <strong>{{ $part->part_no }}</strong></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-info fs-6">{{ $part->qty_available }}</span>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm modal-part-price" value="{{ number_format($part->purchase_price, 2, '.', '') }}" min="0">
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-center modal-part-qty" value="1" min="1">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary btn-add-modal-part" 
                                            data-id="{{ $part->id }}"
                                            data-name="{{ $part->part_no }} - {{ $part->name }}"
                                            data-price="{{ number_format($part->purchase_price, 2, '.', '') }}">
                                        <i class="bx bx-plus me-1"></i> Add to Order
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary d-none" id="btnUpdateModalItem"><i class="bx bx-check me-1"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var itemIndex = {{ count($purchaseOrder->items) }};
    var editingTargetRow = null;

    var itemModalEl = document.getElementById('addItemModal');
    var itemModal = new bootstrap.Modal(itemModalEl);
    var itemModalTitle = document.getElementById('itemModalTitle');
    var modalSearchContainer = document.getElementById('modalSearchContainer');
    var modalTableWrapper = document.getElementById('modalTableWrapper');
    var modalEditPanel = document.getElementById('modalEditPanel');
    var btnUpdateModalItem = document.getElementById('btnUpdateModalItem');
    var modalPartSearch = document.getElementById('modalPartSearch');
    var modalPartsCount = document.getElementById('modalPartsCount');

    function createRow(partId = '', partName = '', qty = 1, price = 0.00) {
        var html = '<div class="item-row row mb-2 align-items-center">' +
            '<div class="col-md-5">' +
                '<input type="hidden" name="items[' + itemIndex + '][spare_part_id]" class="part-id-input" value="' + partId + '" required>' +
                '<input type="text" class="form-control bg-white fw-bold part-name-input" readonly value="' + partName + '" placeholder="Click \'Search & Add Item\' to select part" required>' +
            '</div>' +
            '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control qty text-center" placeholder="Qty" min="1" value="' + qty + '" required></div>' +
            '<div class="col-md-2"><input type="number" step="0.01" name="items[' + itemIndex + '][unit_price]" class="form-control unit-price" placeholder="Price" min="0" value="' + parseFloat(price).toFixed(2) + '"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control line-total" readonly value="' + (qty * price).toFixed(2) + '"></div>' +
            '<div class="col-md-1 text-center"><button type="button" class="btn btn-sm btn-outline-primary btn-edit-item me-1" title="Edit via Modal"><i class="bx bx-edit"></i></button><button type="button" class="btn btn-sm btn-danger remove-item" title="Remove">X</button></div>' +
        '</div>';
        
        $('#itemsContainer').append(html);
        var newRow = $('#itemsContainer').find('.item-row').last();
        itemIndex++;
        calcTotal();
        return newRow;
    }

    // Open Search Modal in Add Mode
    $('#btnOpenSearchModal').click(function() {
        editingTargetRow = null;
        itemModalTitle.innerHTML = '<i class="bx bx-package me-2"></i>Select Spare Parts for Purchase';
        modalSearchContainer.classList.remove('d-none');
        modalTableWrapper.classList.remove('d-none');
        modalEditPanel.classList.add('d-none');
        btnUpdateModalItem.classList.add('d-none');
        modalPartSearch.value = '';
        filterModalParts();
        itemModal.show();
        setTimeout(function() { modalPartSearch.focus(); }, 400);
    });

    // Modal Live Search
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

    // Add Part from Modal
    document.getElementById('modalPartsBody').addEventListener('click', function(e) {
        var addBtn = e.target.closest('.btn-add-modal-part');
        if (!addBtn) return;

        var row = addBtn.closest('.modal-part-row');
        var partId = addBtn.getAttribute('data-id');
        var partName = addBtn.getAttribute('data-name');
        var qtyInput = row.querySelector('.modal-part-qty');
        var priceInput = row.querySelector('.modal-part-price');
        var qty = parseInt(qtyInput.value) || 1;
        var price = parseFloat(priceInput.value) || parseFloat(addBtn.getAttribute('data-price')) || 0;

        var existingRows = $('#itemsContainer .item-row');
        var targetRow = null;

        if (existingRows.length === 1) {
            var firstIdInput = existingRows.eq(0).find('.part-id-input');
            if (!firstIdInput.val()) {
                targetRow = existingRows.eq(0);
            }
        }

        if (targetRow) {
            targetRow.find('.part-id-input').val(partId);
            targetRow.find('.part-name-input').val(partName);
            targetRow.find('.qty').val(qty);
            targetRow.find('.unit-price').val(price.toFixed(2));
            targetRow.find('.line-total').val((qty * price).toFixed(2));
            calcTotal();
        } else {
            createRow(partId, partName, qty, price);
        }

        var originalText = addBtn.innerHTML;
        addBtn.innerHTML = '<i class="bx bx-check me-1"></i> Added!';
        addBtn.classList.remove('btn-primary');
        addBtn.classList.add('btn-success');
        setTimeout(function() {
            addBtn.innerHTML = originalText;
            addBtn.classList.remove('btn-success');
            addBtn.classList.add('btn-primary');
        }, 1000);
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
            alert('At least one item is required in the purchase order.');
        }
        calcTotal();
    });

    // Edit item row via Modal
    $(document).on('click', '.btn-edit-item', function() {
        editingTargetRow = $(this).closest('.item-row');
        var partId = editingTargetRow.find('.part-id-input').val();
        var partName = editingTargetRow.find('.part-name-input').val();

        if (!partId) {
            alert('Please select a spare part first before editing.');
            return;
        }

        var qty = editingTargetRow.find('.qty').val() || 1;
        var price = editingTargetRow.find('.unit-price').val() || 0;

        $('#editPartName').val(partName);
        $('#editQty').val(qty);
        $('#editUnitPrice').val(parseFloat(price).toFixed(2));

        itemModalTitle.innerHTML = '<i class="bx bx-edit me-2"></i>Edit Purchase Order Item';
        modalSearchContainer.classList.add('d-none');
        modalTableWrapper.classList.add('d-none');
        modalEditPanel.classList.remove('d-none');
        btnUpdateModalItem.classList.remove('d-none');

        itemModal.show();
    });

    // Update item row from Modal Edit Panel
    btnUpdateModalItem.addEventListener('click', function() {
        if (!editingTargetRow) return;

        var newQty = parseInt($('#editQty').val()) || 1;
        var newPrice = parseFloat($('#editUnitPrice').val()) || 0;

        editingTargetRow.find('.qty').val(newQty);
        editingTargetRow.find('.unit-price').val(newPrice.toFixed(2));
        editingTargetRow.find('.line-total').val((newQty * newPrice).toFixed(2));
        calcTotal();

        itemModal.hide();
    });

    $(document).on('change keyup', '.qty, .unit-price', function() {
        var row = $(this).closest('.item-row');
        var qty = parseFloat(row.find('.qty').val()) || 0;
        var price = parseFloat(row.find('.unit-price').val()) || 0;
        row.find('.line-total').val((qty * price).toFixed(2));
        calcTotal();
    });

    function calcTotal() {
        var total = 0;
        $('.line-total').each(function() { total += parseFloat($(this).val()) || 0; });
        $('#grandTotal').text(total.toFixed(2));
    }

    calcTotal();
});

document.getElementById('poForm').addEventListener('submit', function(e) {
    var valid = true;
    var items = document.querySelectorAll('.item-row');
    if (items.length === 0) {
        alert('Please add at least one item.');
        e.preventDefault();
        return;
    }
    items.forEach(function(row) {
        var partIdInput = row.querySelector('.part-id-input');
        var qtyInput = row.querySelector('.qty');
        if (partIdInput && !partIdInput.value) {
            valid = false;
            row.querySelector('.part-name-input').classList.add('is-invalid');
        } else if (row.querySelector('.part-name-input')) {
            row.querySelector('.part-name-input').classList.remove('is-invalid');
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

