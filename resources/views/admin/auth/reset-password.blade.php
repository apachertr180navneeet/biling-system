@extends('admin.layouts.login_layout')
@section('content')

<div class="m-auth-wrapper">
    <div class="m-auth-left">
        <div class="m-auth-brand">Mehmaan ERP</div>
        <p class="m-auth-tagline">Create a strong new password to keep your account secure.</p>
    </div>
    <div class="m-auth-right">
        <div class="m-auth-card">
            <div class="m-auth-logo">M</div>
            <h4 class="m-auth-title">Reset Password 🔒</h4>
            <p class="m-auth-subtitle">Please enter your new password below.</p>
            <form method="POST" action="{{ route('admin.reset.password.post') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{$email}}">
                @if ($errors->has('email'))
                <div class="alert alert-danger" style="font-size: 0.8125rem;">{{ $errors->first('email') }}</div>
                @endif
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    @if ($errors->has('password'))
                    <span class="invalid-feedback"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" required>
                    @if ($errors->has('password_confirmation'))
                    <span class="invalid-feedback"><strong>{{ $errors->first('password_confirmation') }}</strong></span>
                    @endif
                </div>
                <div class="mb-3 mt-4">
                    <button type="submit" class="btn btn-primary d-grid w-100">{{ __('Reset Password') }}</button>
                </div>
            </form>
            <div class="text-center">
                <a href="{{route('admin.login')}}" class="m-auth-link d-inline-flex align-items-center gap-1">
                    <i class="bx bx-chevron-left scaleX-n1-rtl"></i>
                    Back to login
                </a>
            </div>
        </div>
    </div>
</div>

@endsection