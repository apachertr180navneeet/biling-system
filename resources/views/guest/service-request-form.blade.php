@extends('web.layouts.app')

@section('title', 'New Service Request - ' . ($company->name ?? config('app.name')))

@section('style')
<style>
    .guest-page { padding: 48px 0; min-height: 70vh; }

    .guest-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 36px; flex-wrap: wrap; gap: 14px;
    }
    .guest-topbar h1 { margin: 0; font-size: 28px; font-weight: 800; }

    .guest-form-card {
        max-width: 700px; margin: 0 auto;
        background: #fff; border: 1px solid #dce4df; overflow: hidden;
    }
    .guest-form-card__header {
        padding: 20px 28px; border-bottom: 1px solid #dce4df;
    }
    .guest-form-card__header h3 { margin: 0; font-size: 18px; font-weight: 700; }
    .guest-form-card__header p { margin: 4px 0 0; color: #66716b; font-size: 14px; }
    .guest-form-card__body { padding: 28px; }

    .guest-field { margin-bottom: 22px; }
    .guest-field label {
        display: block; font-size: 13px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #66716b; margin-bottom: 6px;
    }
    .guest-field label .req { color: #c62828; }
    .guest-field input,
    .guest-field select,
    .guest-field textarea {
        width: 100%; padding: 12px 16px; border: 1px solid #dce4df;
        background: #fff; color: #17211d; font-family: inherit; font-size: 15px;
        outline: none; transition: border-color 180ms ease, box-shadow 180ms ease;
    }
    .guest-field input:focus,
    .guest-field select:focus,
    .guest-field textarea:focus {
        border-color: #14624f; box-shadow: 0 0 0 3px rgba(20, 98, 79, 0.1);
    }
    .guest-field textarea { min-height: 120px; resize: vertical; }

    .guest-field-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 18px;
    }

    .guest-actions {
        display: flex; gap: 12px; padding-top: 8px;
    }

    .alert-error {
        background: #fce4ec; border: 1px solid rgba(198,40,40,0.2);
        color: #c62828; padding: 14px 18px; margin-bottom: 22px; font-size: 14px;
    }

    @media (max-width: 600px) {
        .guest-field-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<section class="guest-page">
    <div class="section-shell">

        <div class="guest-topbar">
            <h1><i class="fas fa-concierge-bell" style="color:#bd8c3a;"></i> New Service Request</h1>
            <a href="{{ route('guest.dashboard') }}" class="btn-outline-site" style="padding:10px 22px; font-size:14px; color:#66716b; border-color:#dce4df;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        @if(session('error'))
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <div class="guest-form-card">
            <div class="guest-form-card__header">
                <h3>Submit a Request</h3>
                <p>Tell us what you need and we'll get it taken care of.</p>
            </div>
            <div class="guest-form-card__body">
                <form action="{{ route('guest.service-request.store') }}" method="POST">
                    @csrf

                    <div class="guest-field-row">
                        <div class="guest-field">
                            <label>Category <span class="req">*</span></label>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="guest-field">
                            <label>Priority <span class="req">*</span></label>
                            <select name="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - When convenient</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium - Within a few hours</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Soon as possible</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Immediate</option>
                            </select>
                        </div>
                    </div>

                    <div class="guest-field">
                        <label>Subject <span class="req">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Brief description of your request" required>
                    </div>

                    <div class="guest-field">
                        <label>Description</label>
                        <textarea name="description" placeholder="Provide more details about your request...">{{ old('description') }}</textarea>
                    </div>

                    <div class="guest-field">
                        <label>Room</label>
                        <select name="room_id">
                            <option value="">Select Room</option>
                            @foreach($reservation->rooms as $rr)
                            <option value="{{ $rr->room_id }}" {{ old('room_id', $reservation->rooms->first()?->room_id) == $rr->room_id ? 'selected' : '' }}>
                                Room {{ $rr->room?->room_number ?? '-' }} - {{ $rr->roomType->name ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="guest-actions">
                        <button type="submit" class="btn-primary-site" style="padding:12px 28px; font-size:15px;">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                        <a href="{{ route('guest.dashboard') }}" class="btn-outline-site" style="padding:12px 28px; font-size:15px; color:#66716b; border-color:#dce4df;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection
