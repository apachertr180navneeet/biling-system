@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Service Reminder</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.service-reminders.update', $serviceReminder) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id', $serviceReminder->customer_id)==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $serviceReminder->vehicle_number) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Service Date</label>
                    <input type="date" name="last_service_date" class="form-control" value="{{ old('last_service_date', $serviceReminder->last_service_date ? $serviceReminder->last_service_date->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next Service Date</label>
                    <input type="date" name="next_service_date" class="form-control @error('next_service_date') is-invalid @enderror" value="{{ old('next_service_date', $serviceReminder->next_service_date->format('Y-m-d')) }}">
                    @error('next_service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reminder Date</label>
                    <input type="date" name="reminder_date" class="form-control" value="{{ old('reminder_date', $serviceReminder->reminder_date ? $serviceReminder->reminder_date->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $serviceReminder->status=='pending'?'selected':'' }}>Pending</option>
                        <option value="sent" {{ $serviceReminder->status=='sent'?'selected':'' }}>Sent</option>
                        <option value="completed" {{ $serviceReminder->status=='completed'?'selected':'' }}>Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control">{{ old('notes', $serviceReminder->notes) }}</textarea>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('admin.service-reminders.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
