@extends('admin.layouts.app')
@section('style')
<style>
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Vehicle Purchase Orders /</span> Create
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New Vehicle Purchase Order</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vehicle-purchase-orders.store') }}" id="poForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Order Date</label>
                    <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}">
                    @error('order_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Expected Date</label>
                    <input type="date" name="expected_date" class="form-control @error('expected_date') is-invalid @enderror" value="{{ old('expected_date') }}">
                    @error('expected_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
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

                <hr>
                <h5>Vehicle Items (Multi-Quantity)</h5>
                <script>var vehiclePrices = @json($vehiclePrices);</script>
                @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div id="itemsContainer">
                    <div class="item-row row">
                        <div class="col-md-4">
                            <select name="items[0][vehicle_description]" class="form-select vehicle-select" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicleList as $opt)
                                <option value="{{ $opt }}" {{ old('items.0.vehicle_description') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="items[0][color_name]" class="form-control" placeholder="Color" value="{{ old('items.0.color_name') }}">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][quantity]" class="form-control qty" placeholder="Qty" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control unit-price" placeholder="Unit Price" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control line-total" readonly value="0.00">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-item">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItem">+ Add Vehicle Row</button>

                <hr>
                <div class="text-end">
                    <h5>Total: <span id="grandTotal">0.00</span></h5>
                </div>
                <button type="submit" class="btn btn-primary">Create PO</button>
                <a href="{{ route('admin.vehicle-purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    var itemIndex = 1;
    $('#addItem').click(function() {
        var optionsHtml = '<option value="">Select Vehicle</option>';
        @foreach($vehicleList as $opt)
        optionsHtml += '<option value="{{ $opt }}">{{ $opt }}</option>';
        @endforeach

        var html = '<div class="item-row row">' +
            '<div class="col-md-4"><select name="items[' + itemIndex + '][vehicle_description]" class="form-select vehicle-select">' + optionsHtml + '</select></div>' +
            '<div class="col-md-2"><input type="text" name="items[' + itemIndex + '][color_name]" class="form-control" placeholder="Color"></div>' +
            '<div class="col-md-1"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control qty" placeholder="Qty" min="1" value="1"></div>' +
            '<div class="col-md-2"><input type="number" step="0.01" name="items[' + itemIndex + '][unit_price]" class="form-control unit-price" placeholder="Unit Price" min="0" value="0"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control line-total" readonly value="0.00"></div>' +
            '<div class="col-md-1"><button type="button" class="btn btn-sm btn-danger remove-item">X</button></div>' +
        '</div>';
        $('#itemsContainer').append(html);
        initSelect2($('#itemsContainer').find('.vehicle-select').last());
        itemIndex++;
    });
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) $(this).closest('.item-row').remove();
        calcTotal();
    });
    $(document).on('change', '.vehicle-select', function() {
        var row = $(this).closest('.item-row');
        var desc = $(this).val();
        if (vehiclePrices[desc]) {
            row.find('.unit-price').val(vehiclePrices[desc]);
            row.find('.qty').trigger('keyup');
        }
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
        alert('Please add at least one item.');
        e.preventDefault();
        return;
    }
    items.forEach(function(row) {
        var partSelect = row.querySelector('select[name*="vehicle_description"]');
        var qtyInput = row.querySelector('input[name*="quantity"]');
        if (partSelect && !partSelect.value) {
            valid = false;
            partSelect.classList.add('is-invalid');
        } else if (partSelect) {
            partSelect.classList.remove('is-invalid');
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
