@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-message-rounded"></i></div>
            <div>
                <h4 class="m-page-title">{{ $template ? 'Edit' : 'Add' }} Template</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.communications.index') }}">Communications</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $template ? 'Edit' : 'Add' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.communications.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ $template ? route('admin.communications.update', $template) : route('admin.communications.store') }}" method="POST">
                @csrf
                @if($template) @method('PUT') @endif
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $template?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Channel <span class="text-danger">*</span></label>
                        <select name="channel" class="form-select" required>
                            <option value="email" {{ old('channel', $template?->channel) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ old('channel', $template?->channel) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="whatsapp" {{ old('channel', $template?->channel) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <input type="text" name="event" class="form-control" value="{{ old('event', $template?->event) }}" required placeholder="e.g. reservation_confirmed">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $template?->subject) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Body <span class="text-danger">*</span></label>
                    <textarea name="body" class="form-control" rows="10" required>{{ old('body', $template?->body) }}</textarea>
                    <small class="text-muted">Use <code>{{ '{{variable}}' }}</code> for dynamic content. Available: guest_name, reservation_number, hotel_name, check_in_date, check_out_date, room_number, amount, otp</small>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Variables (comma separated)</label>
                        <input type="text" name="variables[]" class="form-control" value="{{ old('variables', $template?->variables ? implode(',', $template->variables) : '') }}" placeholder="guest_name, reservation_number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $template?->is_active ?? 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $template?->is_active ?? 1) ? '' : 'selected' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ $template ? 'Update' : 'Create' }} Template</button>
            </form>
        </div>
    </div>
</div>
@endsection
