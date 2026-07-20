@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Create Parts Quotation</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.quotations.store') }}" id="quotationForm">
                @csrf
                <input type="hidden" name="type" value="parts">
                
                <h5 class="card-title text-primary mb-3">Customer Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Select Customer (Existing)</label>
                        <select id="customer_select" name="customer_id" class="form-select">
                            <option value="">-- New Customer / Walk-in --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-name="{{ $c->first_name }} {{ $c->last_name }}"
                                    data-mobile="{{ $c->phone }}"
                                    data-address="{{ $c->address }}"
                                    data-gstin="{{ $c->gstin }}"
                                    data-pan="{{ $c->pan_no }}">
                                {{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})
                            </option>
                            @endforeach
                        </select>
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
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle" id="partsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Spare Part <span class="text-danger">*</span></th>
                                <th style="width: 10%;">GST Type</th>
                                <th style="width: 10%;">Rate</th>
                                <th style="width: 10%;">GST (%)</th>
                                <th style="width: 10%;">Qty</th>
                                <th style="width: 10%;">Total Amount</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="partsTableBody">
                            <!-- Rows added dynamically via JavaScript -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary mt-2" id="addRowBtn">
                        <i class="bx bx-plus"></i> Add Item
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_select');
    const taxRegimeSelect = document.getElementById('tax_regime');
    const addRowBtn = document.getElementById('addRowBtn');
    const tableBody = document.getElementById('partsTableBody');
    
    // Store all spare parts details for easy lookup in JavaScript
    const sparePartsList = @json($spareParts);

    let rowIndex = 0;

    // Customer Selection Change
    customerSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('customer_name').value = option.getAttribute('data-name') || '';
            document.getElementById('customer_mobile').value = option.getAttribute('data-mobile') || '';
            document.getElementById('customer_address').value = option.getAttribute('data-address') || '';
            document.getElementById('customer_gstin').value = option.getAttribute('data-gstin') || '';
            document.getElementById('customer_pan').value = option.getAttribute('data-pan') || '';
        } else {
            document.getElementById('customer_name').value = '';
            document.getElementById('customer_mobile').value = '';
            document.getElementById('customer_address').value = '';
            document.getElementById('customer_gstin').value = '';
            document.getElementById('customer_pan').value = '';
        }
    });

    // Tax Regime Change
    taxRegimeSelect.addEventListener('change', function() {
        const regime = this.value;
        if (regime === 'cgst_sgst') {
            document.querySelectorAll('.cgst-summary, .sgst-summary').forEach(el => el.classList.remove('d-none'));
            document.querySelector('.igst-summary').classList.add('d-none');
        } else {
            document.querySelectorAll('.cgst-summary, .sgst-summary').forEach(el => el.classList.add('d-none'));
            document.querySelector('.igst-summary').classList.remove('d-none');
        }
        calculateTotals();
    });

    // Add Row button click
    addRowBtn.addEventListener('click', function() {
        addNewRow();
    });

    // Add initial row
    addNewRow();

    function addNewRow() {
        const tr = document.createElement('tr');
        tr.setAttribute('data-row-id', rowIndex);

        let optionsHtml = '<option value="">-- Choose Part --</option>';
        sparePartsList.forEach(part => {
            optionsHtml += `<option value="${part.id}" data-price="${part.selling_price}">${part.name} (${part.part_no || ''})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][spare_part_id]" class="form-select part-select select2-enable" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <select name="items[${rowIndex}][gst_type]" class="form-select gst-type" required>
                    <option value="inclusive">Inclusive</option>
                    <option value="exclusive" selected>Exclusive</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIndex}][rate]" class="form-control rate-input" value="0.00" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIndex}][tax_percentage]" class="form-control tax-percentage" value="18.00" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity-input" value="1" required min="1">
            </td>
            <td>
                <span class="row-total fw-bold">₹0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(tr);

        // Bind events for the new row
        const partSelect = tr.querySelector('.part-select');
        const rateInput = tr.querySelector('.rate-input');
        const gstTypeSelect = tr.querySelector('.gst-type');
        const taxInput = tr.querySelector('.tax-percentage');
        const qtyInput = tr.querySelector('.quantity-input');
        const removeBtn = tr.querySelector('.remove-row-btn');

        partSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt.value) {
                rateInput.value = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
            } else {
                rateInput.value = 0;
            }
            calculateRowTotal(tr);
        });

        [rateInput, gstTypeSelect, taxInput, qtyInput].forEach(el => {
            el.addEventListener('input', () => calculateRowTotal(tr));
            el.addEventListener('change', () => calculateRowTotal(tr));
        });

        removeBtn.addEventListener('click', function() {
            tr.remove();
            calculateTotals();
        });

        rowIndex++;
    }

    function calculateRowTotal(row) {
        const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
        const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
        const gstType = row.querySelector('.gst-type').value;
        const taxPercentage = parseFloat(row.querySelector('.tax-percentage').value) || 0;
        const rowTotalSpan = row.querySelector('.row-total');

        const raw_total = rate * qty;
        let amount = 0;

        if (gstType === 'inclusive') {
            amount = raw_total;
        } else {
            amount = raw_total + (raw_total * taxPercentage / 100);
        }

        rowTotalSpan.innerText = '₹' + amount.toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let total_taxable = 0;
        let total_cgst = 0;
        let total_sgst = 0;
        let total_igst = 0;
        let grand_sum = 0;

        const regime = taxRegimeSelect.value;
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
            const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
            const gstType = row.querySelector('.gst-type').value;
            const taxPercentage = parseFloat(row.querySelector('.tax-percentage').value) || 0;

            const raw_total = rate * qty;
            let item_taxable = 0;
            let item_tax = 0;
            let item_amount = 0;

            if (gstType === 'inclusive') {
                item_taxable = raw_total / (1 + (taxPercentage / 100));
                item_tax = raw_total - item_taxable;
                item_amount = raw_total;
            } else {
                item_taxable = raw_total;
                item_tax = (raw_total * taxPercentage) / 100;
                item_amount = raw_total + item_tax;
            }

            total_taxable += item_taxable;
            grand_sum += item_amount;

            if (regime === 'cgst_sgst') {
                total_cgst += item_tax / 2;
                total_sgst += item_tax / 2;
            } else {
                total_igst += item_tax;
            }
        });

        const grandTotalRounded = Math.round(grand_sum);
        const roundOff = grandTotalRounded - grand_sum;

        document.getElementById('summary_taxable').innerText = total_taxable.toFixed(2);
        document.getElementById('summary_cgst').innerText = total_cgst.toFixed(2);
        document.getElementById('summary_sgst').innerText = total_sgst.toFixed(2);
        document.getElementById('summary_igst').innerText = total_igst.toFixed(2);
        document.getElementById('summary_round_off').innerText = roundOff.toFixed(2);
        document.getElementById('summary_grand_total').innerText = grandTotalRounded.toFixed(2);
    }
});
</script>
@endsection
