@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-credit-card"></i></div>
            <div>
                <h4 class="m-page-title">Payment Gateways</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Payment Gateways</li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Payment Gateway Settings</h5>
                    <p class="text-muted mb-0">Configure and manage payment gateways for online payments.</p>
                </div>
                <form method="POST" action="{{ route('admin.payment-gateways.seed') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-refresh me-1"></i>Reset Defaults
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($gateways->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bx bx-credit-card" style="font-size: 3rem; color: var(--m-gray-300);"></i>
            <h5 class="mt-3 text-muted">No Payment Gateways Found</h5>
            <p class="text-muted">Click "Reset Defaults" above to seed Razorpay and Stripe.</p>
        </div>
    </div>
    @else
    <div class="row">
        @foreach($gateways as $gateway)
        <div class="col-lg-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-label-{{ $gateway->slug === 'razorpay' ? 'primary' : 'info' }}">
                                <span class="avatar-initial rounded">
                                    <i class="bx bx-{{ $gateway->slug === 'razorpay' ? 'wallet' : 'credit-card' }}"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $gateway->name }}</h5>
                                <small class="text-muted">{{ $gateway->description }}</small>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input gateway-toggle" type="checkbox"
                                data-url="{{ route('admin.payment-gateways.toggle', $gateway) }}"
                                {{ $gateway->is_active ? 'checked' : '' }}
                                id="toggle-{{ $gateway->slug }}">
                            <label class="form-check-label" for="toggle-{{ $gateway->slug }}">
                                {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge {{ $gateway->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $gateway->is_active ? 'Enabled' : 'Disabled' }}
                            </span>
                            <span class="badge bg-label-{{ $gateway->mode === 'live' ? 'success' : 'warning' }}">
                                {{ strtoupper($gateway->mode) }} Mode
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.payment-gateways.update', $gateway) }}" class="gateway-form">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Environment</label>
                            <select name="mode" class="form-select form-select-sm">
                                <option value="test" {{ $gateway->mode === 'test' ? 'selected' : '' }}>Test Mode</option>
                                <option value="live" {{ $gateway->mode === 'live' ? 'selected' : '' }}>Live Mode</option>
                            </select>
                        </div>

                        @php
                            $credentialFields = match($gateway->slug) {
                                'razorpay' => ['key_id' => 'API Key ID', 'key_secret' => 'API Key Secret'],
                                'stripe' => ['publishable_key' => 'Publishable Key', 'secret_key' => 'Secret Key', 'webhook_secret' => 'Webhook Secret'],
                                default => [],
                            };
                        @endphp

                        @foreach($credentialFields as $key => $label)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <input type="text" name="credentials[{{ $key }}]"
                                class="form-control form-control-sm"
                                value="{{ $gateway->getCredential($key, '') }}"
                                placeholder="Enter {{ $label }}">
                        </div>
                        @endforeach

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-save me-1"></i>Save
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm test-connection"
                                data-url="{{ route('admin.payment-gateways.test', $gateway) }}">
                                <i class="bx bx-plug me-1"></i>Test Connection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-info-circle me-1 text-info"></i>How It Works</h6>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-label-primary rounded-pill">1</span>
                        <div>
                            <strong>Configure</strong>
                            <p class="text-muted mb-0 small">Enter your API credentials and set the mode (Test/Live).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-label-success rounded-pill">2</span>
                        <div>
                            <strong>Test Connection</strong>
                            <p class="text-muted mb-0 small">Click "Test Connection" to verify your credentials are valid.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-label-warning rounded-pill">3</span>
                        <div>
                            <strong>Activate</strong>
                            <p class="text-muted mb-0 small">Toggle the switch to activate the gateway for live payments.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.gateway-toggle').on('change', function() {
        var url = $(this).data('url');
        var checkbox = $(this);
        var label = checkbox.closest('.form-check').find('label');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            beforeSend: function() {
                checkbox.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    label.text(response.is_active ? 'Active' : 'Inactive');
                    var badge = checkbox.closest('.card-body').find('.badge').first();
                    badge.removeClass('bg-success bg-secondary')
                         .addClass(response.is_active ? 'bg-success' : 'bg-secondary')
                         .text(response.is_active ? 'Enabled' : 'Disabled');
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message, timer: 1500, showConfirmButton: false });
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    });

    $('.gateway-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message, timer: 1500, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Something went wrong.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    $('.test-connection').on('click', function() {
        var btn = $(this);
        var url = btn.data('url');

        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Testing...');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Connection Successful', text: response.message, timer: 2500, showConfirmButton: false });
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Connection failed.';
                Swal.fire({ icon: 'error', title: 'Connection Failed', text: msg });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bx bx-plug me-1"></i>Test Connection');
            }
        });
    });
});
</script>
@endsection
