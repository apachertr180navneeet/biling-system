@extends('admin.layouts.login_layout') 
@section('content')

<div class="m-auth-wrapper">
    <div class="m-auth-left">
        <div class="m-auth-brand">Mehmaan ERP</div>
        <p class="m-auth-tagline">Don't worry, we'll help you regain access to your account in no time.</p>
    </div>
    <div class="m-auth-right">
        <div class="m-auth-card">
            <div class="m-auth-logo">M</div>
            <h4 class="m-auth-title">Forgot Password? 🔒</h4>
            <p class="m-auth-subtitle">Enter your email and we'll send you instructions to reset your password.</p>

            @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            <form action="{{route('admin.forget.password.post')}}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail Address</label>
                    <input class="form-control" id="email" type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" autofocus required="">
                    @if ($errors->has('email'))
                    <span class="text-danger" style="font-size: 0.8125rem;">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="mb-3 mt-4">
                    <button type="submit" class="btn btn-primary d-grid w-100">Send Password Reset Link</button>
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