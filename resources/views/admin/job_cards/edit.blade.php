@extends('admin.layouts.app')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
.select2-container--default .select2-selection--single { height: 38px; border: 1px solid #ddd; }
.line-item-row { background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Job Card #{{ $jobCard->job_card_number }}</h4>
        <div>
            @if($jobCard->status != 'billed')
            <button class="btn btn-sm btn-warning update-status" data-status="in_progress">Start Service</button>
            <button class="btn btn-sm btn-success update-status" data-status="completed">Mark Completed</button>
            @endif
            <a href="{{ route('admin.job-cards.print', $jobCard) }}" class="btn btn-sm btn-info" target="_blank"><i class="bx bx-printer"></i> Print</a>
            <a href="{{ route('admin.job-cards.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    @include('admin.layouts.elements.sweet_alerts')

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card"><div class="card-body">
                <h5>Job Card Details</h5>
                <form method="POST" action="{{ route('admin.job-cards.update', $jobCard) }}">
                    @csrf @method('PUT')
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ $jobCard->customer_id==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control" value="{{ $jobCard->vehicle_number }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Model</label>
                            <input type="text" name="vehicle_model" class="form-control" value="{{ $jobCard->vehicle_model }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">KM Reading</label>
                            <input type="text" name="kilometer_reading" class="form-control" value="{{ $jobCard->kilometer_reading }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $jobCard->status=='pending' ? 'selected':'' }}>Pending</option>
                                <option value="in_progress" {{ $jobCard->status=='in_progress' ? 'selected':'' }}>In Progress</option>
                                <option value="completed" {{ $jobCard->status=='completed' ? 'selected':'' }}>Completed</option>
                                <option value="billed" {{ $jobCard->status=='billed' ? 'selected':'' }}>Billed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service Date</label>
                            <input type="date" name="service_date" class="form-control" value="{{ $jobCard->service_date->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Completion Date</label>
                            <input type="date" name="completion_date" class="form-control" value="{{ $jobCard->completion_date ? $jobCard->completion_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Complaint</label>
                            <textarea name="complaint" class="form-control">{{ $jobCard->complaint }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control">{{ $jobCard->notes }}</textarea>
                        </div>
                    </div>
                    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update</button></div>
                </form>
            </div></div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card"><div class="card-body">
                <h5>Billing Summary</h5>
                <table class="table table-sm">
                    <tr><td>Total Labor:</td><td class="text-end">{{ number_format($jobCard->total_labor, 2) }}</td></tr>
                    <tr><td>Total Parts:</td><td class="text-end">{{ number_format($jobCard->total_parts, 2) }}</td></tr>
                    <tr><td>Subtotal:</td><td class="text-end">{{ number_format($jobCard->subtotal, 2) }}</td></tr>
                    @if($jobCard->is_gst)
                    <tr><td>GST ({{ strtoupper($jobCard->gst_type ?? '-') }}):</td><td class="text-end">{{ number_format($jobCard->gst_amount, 2) }}</td></tr>
                    @endif
                    <tr class="fw-bold"><td>Grand Total:</td><td class="text-end">{{ number_format($jobCard->grand_total, 2) }}</td></tr>
                </table>

                <hr>
                <h5>Line Items</h5>
                <form id="billingForm">
                    @csrf
                    <div id="itemsContainer">
                        @foreach($jobCard->services as $svc)
                        <div class="line-item-row">
                            <div class="row g-2">
                                <div class="col-3">
                                    <select class="form-select item-type"><option value="service" selected>Service</option><option value="part">Part</option></select>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control item-name" value="{{ $svc->service_name }}">
                                </div>
                                <div class="col-2">
                                    <input type="number" class="form-control item-qty" value="1" min="1" step="1">
                                </div>
                                <div class="col-2">
                                    <input type="number" step="0.01" class="form-control item-rate" value="{{ $svc->labor_charge }}">
                                </div>
                                <div class="col-1">
                                    <input type="number" class="form-control item-gst" value="0" placeholder="GST%">
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bx bx-x"></i></button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @foreach($jobCard->parts as $part)
                        <div class="line-item-row">
                            <div class="row g-2">
                                <div class="col-3">
                                    <select class="form-select item-type"><option value="service">Service</option><option value="part" selected>Part</option></select>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control item-name" value="{{ $part->part_name }}">
                                </div>
                                <div class="col-2">
                                    <input type="number" class="form-control item-qty" value="{{ $part->quantity }}" min="1" step="0.01">
                                </div>
                                <div class="col-2">
                                    <input type="number" step="0.01" class="form-control item-rate" value="{{ $part->rate }}">
                                </div>
                                <div class="col-1">
                                    <input type="number" class="form-control item-gst" value="{{ $part->gst_rate }}" placeholder="GST%">
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bx bx-x"></i></button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-secondary" id="addServiceItem"><i class="bx bx-plus"></i> Add Service</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="addPartItem"><i class="bx bx-plus"></i> Add Part</button>
                        <button type="button" class="btn btn-primary btn-sm" id="calculateBilling"><i class="bx bx-calculator"></i> Calculate & Save</button>
                    </div>
                </form>
            </div></div>
        </div>
    </div>
</div>

<form id="statusForm" method="POST">@csrf</form>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('.update-status').click(function(){
        if(!confirm('Change status to '+$(this).data('status')+'?')) return;
        var form=$('#statusForm');
        form.append('<input type="hidden" name="status" value="'+$(this).data('status')+'">');
        $.post('{{ route("admin.job-cards.update-status", $jobCard) }}', form.serialize()).done(function(r){
            if(r.success) location.reload();
        }).fail(function(){alert('Error updating status');});
    });

    function createItemRow(type, name, qty, rate, gst) {
        const t = type || 'service';
        const n = name || '';
        const q = qty || 1;
        const r = rate || 0;
        const g = gst || 0;
        return `
        <div class="line-item-row">
            <div class="row g-2">
                <div class="col-3">
                    <select class="form-select item-type"><option value="service" ${t=='service'?'selected':''}>Service</option><option value="part" ${t=='part'?'selected':''}>Part</option></select>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control item-name" value="${n}">
                </div>
                <div class="col-2">
                    <input type="number" class="form-control item-qty" value="${q}" min="1" step="0.01">
                </div>
                <div class="col-2">
                    <input type="number" step="0.01" class="form-control item-rate" value="${r}">
                </div>
                <div class="col-1">
                    <input type="number" class="form-control item-gst" value="${g}" placeholder="GST%">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bx bx-x"></i></button>
                </div>
            </div>
        </div>`;
    }

    $('#addServiceItem').click(function(){
        $('#itemsContainer').append(createItemRow('service', '', 1, 0, 0));
    });
    $('#addPartItem').click(function(){
        $('#itemsContainer').append(createItemRow('part', '', 1, 0, 0));
    });
    $(document).on('click', '.remove-item', function(){ $(this).closest('.line-item-row').remove(); });

    $('#calculateBilling').click(function(){
        var items = [];
        $('#itemsContainer .line-item-row').each(function(){
            items.push({
                type: $(this).find('.item-type').val(),
                name: $(this).find('.item-name').val(),
                qty: $(this).find('.item-qty').val(),
                rate: $(this).find('.item-rate').val(),
                gst_rate: $(this).find('.item-gst').val() || 0
            });
        });
        if(!items.length){ alert('Add at least one item.'); return; }
        $.ajax({
            url: '{{ route("admin.job-cards.calculate-billing", $jobCard) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', items: items },
            success: function(r){
                if(r.success) { setFlesh('Billing calculated successfully.','success'); setTimeout(function(){ location.reload(); }, 1000); }
            },
            error: function(xhr){ alert('Error: '+(xhr.responseJSON?.message||'unknown')); }
        });
    });
});
</script>
@endpush
@endsection
