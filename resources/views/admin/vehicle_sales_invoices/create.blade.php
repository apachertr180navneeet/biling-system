@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Create Vehicle Sales Invoice</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vehicle-sales-invoices.store') }}" id="invoiceForm">
                @csrf
                
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
                                    data-address="{{ $c->address }}">
                                {{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" id="customer_mobile" name="customer_mobile" class="form-control @error('customer_mobile') is-invalid @enderror" value="{{ old('customer_mobile') }}">
                        @error('customer_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" id="customer_age" name="customer_age" class="form-control @error('customer_age') is-invalid @enderror" value="{{ old('customer_age') }}">
                        @error('customer_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Occupation</label>
                        <input type="text" id="customer_occupation" name="customer_occupation" class="form-control @error('customer_occupation') is-invalid @enderror" value="{{ old('customer_occupation') }}">
                        @error('customer_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Residence Tel. Ph.</label>
                        <input type="text" id="customer_residence_phone" name="customer_residence_phone" class="form-control @error('customer_residence_phone') is-invalid @enderror" value="{{ old('customer_residence_phone') }}">
                        @error('customer_residence_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-select">
                            <option value="Cash">Cash</option>
                            <option value="UPI / Online">UPI / Online</option>
                            <option value="Card">Card</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Permanent Address</label>
                        <textarea id="customer_address" name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" rows="2">{{ old('customer_address') }}</textarea>
                        @error('customer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="card-title text-primary mb-3">Vehicle Selection</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Select Available Vehicle <span class="text-danger">*</span></label>
                        <select id="vehicle_select" name="vehicle_inventory_id" class="form-select @error('vehicle_inventory_id') is-invalid @enderror" required>
                            <option value="">-- Choose Vehicle --</option>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}"
                                    data-desc="{{ $v->vehicle_description }}"
                                    data-chassis="{{ $v->chassis_number }}"
                                    data-motor="{{ $v->motor_number }}"
                                    data-battery-no="{{ $v->battery_number }}"
                                    data-charger-no="{{ $v->charger_number }}"
                                    data-controller-no="{{ $v->controller_number }}"
                                    data-convertor-no="{{ $v->convertor_number }}"
                                    data-manual-no="{{ $v->manual_number }}"
                                    data-battery-type="{{ $v->battery_type }}"
                                    data-battery-make="{{ $v->battery_make }}"
                                    data-rate="{{ $v->ex_showroom_price }}">
                                {{ $v->vehicle_description }} - Chassis: {{ $v->chassis_number }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_inventory_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Live Preview of Selected Vehicle Details -->
                <div id="vehicle_details_card" class="card mb-4 bg-light d-none border border-light-subtle">
                    <div class="card-body">
                        <h6 class="fw-semibold text-secondary mb-3"><i class="bx bx-car me-1"></i> Selected Vehicle Specifications</h6>
                        <div class="row g-3">
                            <div class="col-md-4"><strong>Model/Description:</strong> <span id="lbl_desc">-</span></div>
                            <div class="col-md-4"><strong>Chassis No:</strong> <span id="lbl_chassis">-</span></div>
                            <div class="col-md-4"><strong>Motor No:</strong> <span id="lbl_motor">-</span></div>
                            <div class="col-md-4"><strong>Battery No:</strong> <span id="lbl_battery_no">-</span></div>
                            <div class="col-md-4"><strong>Charger No:</strong> <span id="lbl_charger_no">-</span></div>
                            <div class="col-md-4"><strong>Controller No:</strong> <span id="lbl_controller_no">-</span></div>
                            <div class="col-md-4"><strong>Convertor No:</strong> <span id="lbl_convertor_no">-</span></div>
                            <div class="col-md-4"><strong>Manual No:</strong> <span id="lbl_manual_no">-</span></div>
                            <div class="col-md-4"><strong>Battery Type:</strong> <span id="lbl_battery_type">-</span></div>
                            <div class="col-md-4"><strong>Battery Make:</strong> <span id="lbl_battery_make">-</span></div>
                        </div>
                    </div>
                </div>

                <h5 class="card-title text-primary mb-3">Invoice & Pricing Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rate / Ex-Showroom Price (INR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="rate" name="rate" class="form-control @error('rate') is-invalid @enderror" value="{{ old('rate', 0) }}" required>
                        @error('rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SGST @ 2.5% (Calculated)</label>
                        <input type="text" id="sgst" class="form-control bg-light" readonly value="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">CGST @ 2.5% (Calculated)</label>
                        <input type="text" id="cgst" class="form-control bg-light" readonly value="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subtotal Incl. GST</label>
                        <input type="text" id="subtotal_incl_gst" class="form-control bg-light" readonly value="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Less: NEMMP 2020 Incentive</label>
                        <input type="number" step="0.01" id="nemmp_incentive" name="nemmp_incentive" class="form-control" value="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Less: Discount/Offer</label>
                        <input type="number" step="0.01" id="discount" name="discount" class="form-control" value="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grand Total (INR)</label>
                        <input type="text" id="grand_total" class="form-control bg-light fw-bold text-success" readonly value="0.00">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Warranty Terms / Notes</label>
                    <textarea name="warranty_notes" class="form-control" rows="3">MOTOR, CONTROLLER WARRANTY - 1 YEAR
BATTERY WARRANTY - 3 YEAR
CHARGER WARRANTY - 2 YEAR</textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Generate Invoice</button>
                    <a href="{{ route('admin.vehicle-sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
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
    
    customerSelect.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt.value) {
            customerNameInput.value = opt.getAttribute('data-name') || '';
            customerMobileInput.value = opt.getAttribute('data-mobile') || '';
            customerAddressInput.value = opt.getAttribute('data-address') || '';
        } else {
            customerNameInput.value = '';
            customerMobileInput.value = '';
            customerAddressInput.value = '';
        }
    });

    var vehicleSelect = document.getElementById('vehicle_select');
    var detailsCard = document.getElementById('vehicle_details_card');
    
    var lblDesc = document.getElementById('lbl_desc');
    var lblChassis = document.getElementById('lbl_chassis');
    var lblMotor = document.getElementById('lbl_motor');
    var lblBatteryNo = document.getElementById('lbl_battery_no');
    var lblChargerNo = document.getElementById('lbl_charger_no');
    var lblControllerNo = document.getElementById('lbl_controller_no');
    var lblConvertorNo = document.getElementById('lbl_convertor_no');
    var lblManualNo = document.getElementById('lbl_manual_no');
    var lblBatteryType = document.getElementById('lbl_battery_type');
    var lblBatteryMake = document.getElementById('lbl_battery_make');
    var rateInput = document.getElementById('rate');

    vehicleSelect.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt.value) {
            lblDesc.textContent = opt.getAttribute('data-desc') || '-';
            lblChassis.textContent = opt.getAttribute('data-chassis') || '-';
            lblMotor.textContent = opt.getAttribute('data-motor') || '-';
            lblBatteryNo.textContent = opt.getAttribute('data-battery-no') || '-';
            lblChargerNo.textContent = opt.getAttribute('data-charger-no') || '-';
            lblControllerNo.textContent = opt.getAttribute('data-controller-no') || '-';
            lblConvertorNo.textContent = opt.getAttribute('data-convertor-no') || '-';
            lblManualNo.textContent = opt.getAttribute('data-manual-no') || '-';
            lblBatteryType.textContent = opt.getAttribute('data-battery-type') || '-';
            lblBatteryMake.textContent = opt.getAttribute('data-battery-make') || '-';
            rateInput.value = opt.getAttribute('data-rate') || '0';
            
            detailsCard.classList.remove('d-none');
        } else {
            detailsCard.classList.add('d-none');
            rateInput.value = '0';
        }
        calculateInvoice();
    });

    var rateInp = document.getElementById('rate');
    var nemmpInp = document.getElementById('nemmp_incentive');
    var discountInp = document.getElementById('discount');
    
    var sgstOut = document.getElementById('sgst');
    var cgstOut = document.getElementById('cgst');
    var subtotalOut = document.getElementById('subtotal_incl_gst');
    var grandTotalOut = document.getElementById('grand_total');

    function calculateInvoice() {
        var rate = parseFloat(rateInp.value) || 0;
        var cgst = Math.round(rate * 2.5) / 100;
        var sgst = Math.round(rate * 2.5) / 100;
        var subtotal = rate + cgst + sgst;
        
        var nemmp = parseFloat(nemmpInp.value) || 0;
        var discount = parseFloat(discountInp.value) || 0;
        var grand = subtotal - nemmp - discount;

        sgstOut.value = sgst.toFixed(2);
        cgstOut.value = cgst.toFixed(2);
        subtotalOut.value = subtotal.toFixed(2);
        grandTotalOut.value = grand.toFixed(2);
    }

    rateInp.addEventListener('input', calculateInvoice);
    nemmpInp.addEventListener('input', calculateInvoice);
    discountInp.addEventListener('input', calculateInvoice);
});
</script>
@endsection
