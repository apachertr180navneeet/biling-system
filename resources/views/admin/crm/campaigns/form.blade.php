@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-paper-plane"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($campaign) ? 'Edit Campaign' : 'Create Campaign' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.campaigns.index') }}">Campaigns</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($campaign) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($campaign) ? route('admin.crm.campaigns.update', $campaign) : route('admin.crm.campaigns.store') }}" method="POST">
                @csrf
                @if(isset($campaign)) @method('PUT') @endif

                <div class="m-section-divider">Campaign Configuration</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Summer Special Offer 2026" value="{{ old('name', $campaign?->name) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Channel <span class="text-danger">*</span></label>
                        <select name="channel" id="campaign-channel" class="form-select" required>
                            <option value="">Select Channel</option>
                            @foreach(['email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('channel', $campaign?->channel) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select name="target_audience" class="form-select" required>
                            @foreach(['all_guests' => 'All Guests', 'loyalty_members' => 'Loyalty Members', 'recent_guests' => 'Recent Guests (Last 15)'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('target_audience', $campaign?->target_audience) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row id-email-only" style="display: none;">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Subject Line <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="campaign-subject" class="form-control" placeholder="e.g. Exquisite Deals Await You! 🌟" value="{{ old('subject', $campaign?->subject) }}">
                    </div>
                </div>

                <div class="m-section-divider">Message Content</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Body Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="8" placeholder="Type your message content here. For SMS or WhatsApp, keep it concise. For Emails, HTML tags are supported." required>{{ old('content', $campaign?->content) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Scheduling & Status</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $campaign?->scheduled_at?->format('Y-m-d\TH:i')) }}">
                        <div class="form-text text-muted">Leave empty to dispatch manually.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled / Active'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $campaign?->status ?? 'draft') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($campaign) ? 'Update' : 'Create' }} Campaign</button>
                    <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    function toggleSubject() {
        var channel = $('#campaign-channel').val();
        if (channel === 'email') {
            $('.id-email-only').slideDown();
            $('#campaign-subject').attr('required', true);
        } else {
            $('.id-email-only').slideUp();
            $('#campaign-subject').removeAttr('required').val('');
        }
    }

    $('#campaign-channel').on('change', toggleSubject);
    toggleSubject(); // Run on load
});
</script>
@endsection
