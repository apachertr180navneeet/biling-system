@extends('admin.layouts.app')
@section('style')
<style>
.vehicle-rows .vehicle-row { border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px; margin-bottom: 8px; background: #fafafa; }
.vehicle-rows .vehicle-row .btn-remove { color: #dc3545; cursor: pointer; }
.validation-icon { width: 20px; display: inline-block; }
.validation-icon .spinner { display: none; width: 14px; height: 14px; border: 2px solid #e0e0e0; border-top-color: #696cff; border-radius: 50%; animation: spin 0.6s linear infinite; }
.validation-icon .check { display: none; color: #28a745; font-weight: bold; }
.validation-icon .error { display: none; color: #dc3545; font-weight: bold; }
.validation-icon.checking .spinner { display: inline-block; }
.validation-icon.checking .check,
.validation-icon.checking .error { display: none; }
.validation-icon.valid .check { display: inline-block; }
.validation-icon.valid .spinner,
.validation-icon.valid .error { display: none; }
.validation-icon.invalid .error { display: inline-block; }
.validation-icon.invalid .spinner,
.validation-icon.invalid .check { display: none; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Receive Vehicles - {{ $vehiclePurchaseOrder->po_number }}</h4>
    <div class="card"><div class="card-body">
        <p><strong>Supplier:</strong> {{ $vehiclePurchaseOrder->supplier->name ?? '-' }}</p>
        <p><strong>Order Date:</strong> {{ $vehiclePurchaseOrder->order_date->format('d-m-Y') }}</p>
        <hr>
        <form method="POST" action="{{ route('admin.vehicle-purchase-orders.receive-store', $vehiclePurchaseOrder) }}" id="receiveForm">
            @csrf
            <div id="deleted-vehicles-container"></div>
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            @foreach($vehiclePurchaseOrder->items as $i => $item)
                @php $remaining = $item->quantity - $item->received_quantity; @endphp
                @if($remaining > 0)
                <div class="card mb-3" id="po-item-{{ $item->id }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $item->vehicle_description }}</strong>
                            @if($item->color_name) <span class="badge bg-secondary">{{ $item->color_name }}</span> @endif
                            @if($item->mfg_year) <span class="badge bg-info">{{ $item->mfg_year }}</span> @endif
                        </div>
                        <div>
                            <span class="text-muted">Ordered: {{ $item->quantity }}</span> |
                            <span class="text-muted">Received: {{ $item->received_quantity }}</span> |
                            <span class="text-success fw-bold">Remaining: {{ $remaining }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        
                        @if(isset($receivedVehicles) && $receivedVehicles->isNotEmpty())
                            @php
                                $itemVehicles = $receivedVehicles->filter(function($v) use ($item) {
                                    return $v->vehicle_description === $item->vehicle_description
                                        && $v->color_name === $item->color_name
                                        && $v->mfg_year === $item->mfg_year;
                                });
                            @endphp
                            @if($itemVehicles->isNotEmpty())
                                <div class="mb-4 p-3 bg-light rounded border border-light-subtle">
                                    <div class="small fw-semibold text-muted mb-3"><i class="bx bx-edit me-1"></i> Edit Previously Received Vehicles ({{ $itemVehicles->count() }}):</div>
                                    <div class="edit-vehicle-rows">
                                        @foreach($itemVehicles as $rev)
                                            <div class="vehicle-row d-flex align-items-center gap-2 mb-2">
                                                <input type="hidden" name="edit_vehicles[{{ $rev->id }}][id]" value="{{ $rev->id }}">
                                                <div class="flex-grow-1">
                                                    <label class="form-label small text-muted">Chassis Number *</label>
                                                    <input type="text" name="edit_vehicles[{{ $rev->id }}][chassis_number]" class="form-control bg-white" required maxlength="255" data-field="chassis_number" data-id="{{ $rev->id }}" value="{{ old("edit_vehicles.{$rev->id}.chassis_number", $rev->chassis_number) }}" style="{{ $errors->has("edit_vehicles.{$rev->id}.chassis_number") ? 'border: 2px solid #dc3545;' : '' }}">
                                                    <div class="validation-message small text-danger mt-1">
                                                        @error("edit_vehicles.{$rev->id}.chassis_number")
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label small text-muted">Engine Number *</label>
                                                    <input type="text" name="edit_vehicles[{{ $rev->id }}][engine_number]" class="form-control bg-white" required maxlength="255" data-field="engine_number" data-id="{{ $rev->id }}" value="{{ old("edit_vehicles.{$rev->id}.engine_number", $rev->engine_number) }}" style="{{ $errors->has("edit_vehicles.{$rev->id}.engine_number") ? 'border: 2px solid #dc3545;' : '' }}">
                                                    <div class="validation-message small text-danger mt-1">
                                                        @error("edit_vehicles.{$rev->id}.engine_number")
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div style="width:40px;" class="d-flex align-items-end justify-content-center">
                                                    <button type="button" class="btn btn-link btn-remove-received p-0 align-middle" data-id="{{ $rev->id }}"><i class="bx bx-trash text-danger" style="font-size: 1.5rem;"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="vehicle-rows" id="vehicles-{{ $item->id }}">
                            @php
                                $oldVehicles = old("items.{$i}.vehicles");
                                $vehiclesToRender = is_array($oldVehicles) ? $oldVehicles : [['chassis_number' => '', 'engine_number' => '']];
                            @endphp
                            @foreach($vehiclesToRender as $vIdx => $vVal)
                            <div class="vehicle-row d-flex align-items-center gap-2">
                                <div class="flex-grow-1">
                                    <label class="form-label small">Chassis Number *</label>
                                    <input type="text" name="items[{{ $i }}][vehicles][{{ $vIdx }}][chassis_number]" class="form-control" required maxlength="255" data-field="chassis_number" value="{{ old("items.{$i}.vehicles.{$vIdx}.chassis_number", $vVal['chassis_number'] ?? '') }}" style="{{ $errors->has("items.{$i}.vehicles.{$vIdx}.chassis_number") ? 'border: 2px solid #dc3545;' : '' }}">
                                    <div class="validation-message small text-danger mt-1">
                                        @error("items.{$i}.vehicles.{$vIdx}.chassis_number")
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label small">Engine Number *</label>
                                    <input type="text" name="items[{{ $i }}][vehicles][{{ $vIdx }}][engine_number]" class="form-control" required maxlength="255" data-field="engine_number" value="{{ old("items.{$i}.vehicles.{$vIdx}.engine_number", $vVal['engine_number'] ?? '') }}" style="{{ $errors->has("items.{$i}.vehicles.{$vIdx}.engine_number") ? 'border: 2px solid #dc3545;' : '' }}">
                                    <div class="validation-message small text-danger mt-1">
                                        @error("items.{$i}.vehicles.{$vIdx}.engine_number")
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div style="width:40px;" class="d-flex align-items-end justify-content-center">
                                    @if($vIdx > 0)
                                        <button type="button" class="btn btn-link btn-remove p-0 align-middle"><i class="bx bx-trash text-danger" style="font-size: 1.5rem;"></i></button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-vehicle-btn" data-item="{{ $item->id }}" data-remaining="{{ $remaining }}" data-max="{{ $remaining }}">
                            <i class="bx bx-plus"></i> Add Vehicle
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Receive Vehicles</button>
                <a href="{{ route('admin.vehicle-purchase-orders.show', $vehiclePurchaseOrder) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
@section('script')
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function checkUnique(input) {
    var field = input.dataset.field;
    var value = input.value.trim();
    var lowerVal = value.toLowerCase();
    var row = input.closest('.vehicle-row');
    var msgBox = input.parentNode.querySelector('.validation-message');

    if (!value) {
        msgBox.textContent = '';
        input.style.border = '';
        clearDuplicateErrors(field);
        return;
    }

    // 1. Same row chassis !== engine check
    if (row) {
        var chassisInput = row.querySelector('input[data-field="chassis_number"]');
        var engineInput = row.querySelector('input[data-field="engine_number"]');
        if (chassisInput && engineInput && chassisInput.value.trim() && engineInput.value.trim() && chassisInput.value.trim() === engineInput.value.trim()) {
            chassisInput.style.border = '2px solid #dc3545';
            engineInput.style.border = '2px solid #dc3545';
            var chassisMsg = chassisInput.parentNode.querySelector('.validation-message');
            var engineMsg = engineInput.parentNode.querySelector('.validation-message');
            if (chassisMsg) { chassisMsg.textContent = 'Chassis and engine number must be different.'; chassisMsg.className = 'validation-message small text-danger mt-1'; }
            if (engineMsg) { engineMsg.textContent = 'Chassis and engine number must be different.'; engineMsg.className = 'validation-message small text-danger mt-1'; }
            return;
        } else if (chassisInput && engineInput) {
            // clear cross-field error if values now differ
            var cMsg = chassisInput.parentNode.querySelector('.validation-message');
            var eMsg = engineInput.parentNode.querySelector('.validation-message');
            if (cMsg && cMsg.textContent === 'Chassis and engine number must be different.') { cMsg.textContent = ''; cMsg.className = 'validation-message small mt-1'; chassisInput.style.border = ''; }
            if (eMsg && eMsg.textContent === 'Chassis and engine number must be different.') { eMsg.textContent = ''; eMsg.className = 'validation-message small mt-1'; engineInput.style.border = ''; }
        }
    }

    // 2. Unique within form check
    var inputsOfSameField = document.querySelectorAll('input[data-field="' + field + '"]');
    var duplicateFound = false;
    inputsOfSameField.forEach(function(otherInput) {
        if (otherInput !== input && otherInput.value.trim().toLowerCase() === lowerVal) {
            duplicateFound = true;
            otherInput.style.border = '2px solid #dc3545';
            var otherMsg = otherInput.parentNode.querySelector('.validation-message');
            if (otherMsg) {
                var label = field === 'chassis_number' ? 'Chassis number' : 'Engine number';
                otherMsg.textContent = 'Duplicate ' + label.toLowerCase() + ' in this form.';
                otherMsg.className = 'validation-message small text-danger mt-1';
            }
        }
    });

    if (duplicateFound) {
        input.style.border = '2px solid #dc3545';
        var label = field === 'chassis_number' ? 'Chassis number' : 'Engine number';
        msgBox.textContent = 'Duplicate ' + label.toLowerCase() + ' in this form.';
        msgBox.className = 'validation-message small text-danger mt-1';
        return;
    }

    // 3. Unique in DB check
    input.style.border = '1px solid #aaa';
    msgBox.textContent = 'Checking...';
    msgBox.className = 'validation-message small text-muted mt-1';

    checkUniqueDB(input, value);
}

function clearDuplicateErrors(field) {
    revalidateFieldType(field);
}

function revalidateFieldType(field) {
    var inputs = document.querySelectorAll('input[data-field="' + field + '"]');
    var values = {};
    inputs.forEach(function(inp) {
        var val = inp.value.trim().toLowerCase();
        if (val) {
            values[val] = (values[val] || 0) + 1;
        }
    });

    inputs.forEach(function(inp) {
        var val = inp.value.trim().toLowerCase();
        var msgBox = inp.parentNode.querySelector('.validation-message');
        if (msgBox && msgBox.textContent.indexOf('Duplicate') === 0) {
            if (!val || values[val] <= 1) {
                msgBox.textContent = '';
                msgBox.className = 'validation-message small mt-1';
                inp.style.border = '';
                if (val) {
                    checkUniqueDB(inp, inp.value.trim());
                }
            }
        }
    });
}

function checkUniqueDB(input, value) {
    var field = input.dataset.field;
    var msgBox = input.parentNode.querySelector('.validation-message');
    var ignoreId = input.dataset.id || '';
    
    var formData = new FormData();
    formData.append('field', field);
    formData.append('value', value);
    if (ignoreId) {
        formData.append('ignore_id', ignoreId);
    }

    fetch('{{ route("admin.vehicle-inventories.check-unique") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (input.value.trim() !== value) return; // value changed in the meantime
        if (data.valid) {
            input.style.border = '2px solid #28a745';
            msgBox.textContent = '';
            msgBox.className = 'validation-message small mt-1';
            revalidateFieldType(field);
        } else {
            input.style.border = '2px solid #dc3545';
            msgBox.textContent = data.message;
            msgBox.className = 'validation-message small text-danger mt-1';
        }
    })
    .catch(function() {
        if (input.value.trim() === value) {
            input.style.border = '';
            msgBox.textContent = '';
            msgBox.className = 'validation-message small mt-1';
        }
    });
}

document.addEventListener('input', function(e) {
    if (e.target.matches('input[data-field]')) {
        checkUnique(e.target);
    }
});

document.querySelectorAll('.add-vehicle-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var container = document.getElementById('vehicles-' + this.dataset.item);
        var rows = container.querySelectorAll('.vehicle-row');
        var remaining = parseInt(this.dataset.remaining);
        if (rows.length >= remaining) {
            alert('Cannot add more vehicles. Remaining quantity is ' + remaining + '.');
            return;
        }
        var newIndex = rows.length;
        var newRow = rows[0].cloneNode(true);
        newRow.querySelectorAll('input').forEach(function(input) {
            input.value = '';
            input.style.border = '';
            var name = input.name;
            name = name.replace(/\[vehicles\]\[\d+\]/, '[vehicles][' + newIndex + ']');
            input.name = name;
            var msg = input.parentNode.querySelector('.validation-message');
            if (msg) { msg.textContent = ''; msg.className = 'validation-message small mt-1'; }
        });
        
        var actionDiv = newRow.querySelector('div[style*="width:40px"]');
        if (actionDiv) {
            actionDiv.className = 'd-flex align-items-end justify-content-center';
            actionDiv.innerHTML = '<button type="button" class="btn btn-link btn-remove p-0 align-middle"><i class="bx bx-trash text-danger" style="font-size: 1.5rem;"></i></button>';
        }
        container.appendChild(newRow);
    });
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove')) {
        var row = e.target.closest('.vehicle-row');
        var container = row.parentNode;
        row.remove();
        
        var rows = container.querySelectorAll('.vehicle-row');
        rows.forEach(function(r, index) {
            r.querySelectorAll('input').forEach(function(input) {
                var name = input.name;
                name = name.replace(/\[vehicles\]\[\d+\]/, '[vehicles][' + index + ']');
                input.name = name;
            });
            var actionDiv = r.querySelector('div[style*="width:40px"]');
            if (actionDiv) {
                if (index === 0) {
                    actionDiv.innerHTML = '';
                } else if (!actionDiv.querySelector('.btn-remove')) {
                    actionDiv.innerHTML = '<button type="button" class="btn btn-link btn-remove p-0 align-middle"><i class="bx bx-trash text-danger" style="font-size: 1.5rem;"></i></button>';
                }
            }
        });
        
        revalidateFieldType('chassis_number');
        revalidateFieldType('engine_number');
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-received')) {
        if (!confirm('Are you sure you want to remove this vehicle from inventory?')) {
            return;
        }
        var btn = e.target.closest('.btn-remove-received');
        var id = btn.dataset.id;
        var row = btn.closest('.vehicle-row');
        
        var container = document.getElementById('deleted-vehicles-container');
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'delete_vehicles[]';
        hiddenInput.value = id;
        container.appendChild(hiddenInput);
        
        row.remove();
        
        revalidateFieldType('chassis_number');
        revalidateFieldType('engine_number');
    }
});

document.getElementById('receiveForm').addEventListener('submit', function(e) {
    var inputs = this.querySelectorAll('input[data-field]');
    var errors = [];
    
    inputs.forEach(function(input) {
        var val = input.value.trim();
        if (!val) {
            var label = input.dataset.field === 'chassis_number' ? 'Chassis number' : 'Engine number';
            errors.push(label + ' is required for all vehicles.');
        }
        if (input.style.border.indexOf('rgb(220, 53, 69)') !== -1 || input.style.border.indexOf('#dc3545') !== -1 || input.style.borderColor === 'rgb(220, 53, 69)' || input.style.borderColor === '#dc3545') {
            var msg = input.parentNode.querySelector('.validation-message');
            var errorText = msg ? msg.textContent.trim() : '';
            errors.push(errorText || ('Please fix invalid ' + input.dataset.field.replace('_', ' ') + ' values before submitting.'));
        }
    });
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors[0]);
    }
});
</script>
@endsection
