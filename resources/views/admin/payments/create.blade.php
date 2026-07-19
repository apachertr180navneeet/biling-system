@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Record Payment</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">Select Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Invoice (optional)</label>
                    <select name="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror">
                        <option value="">Select Invoice</option>
                        @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" {{ old('invoice_id')==$inv->id ? 'selected':'' }}>{{ $inv->invoice_number }} - {{ number_format($inv->grand_total,2) }}</option>
                        @endforeach
                    </select>
                    @error('invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}">
                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}">
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror">
                        <option value="">Select</option>
                        <option value="cash" {{ old('payment_mode')=='cash' ? 'selected':'' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_mode')=='bank_transfer' ? 'selected':'' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_mode')=='cheque' ? 'selected':'' }}>Cheque</option>
                        <option value="upi" {{ old('payment_mode')=='upi' ? 'selected':'' }}>UPI</option>
                    </select>
                    @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference No</label>
                    <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}">
                    @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Save Payment</button> <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
