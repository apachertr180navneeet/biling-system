@extends('admin.layouts.login_layout') 
@section('content') 

<div class="m-auth-wrapper">
    <div class="m-auth-left">
        <div class="m-auth-brand">Mehmaan ERP</div>
        <p class="m-auth-tagline">Streamline your business operations with our powerful enterprise resource planning solution.</p>
    </div>
    <div class="m-auth-right">
        <div class="m-auth-card">
            <div class="m-auth-logo">M</div>
            <h4 class="m-auth-title">Welcome back! 👋</h4>
            <p class="m-auth-subtitle">Please sign in to your admin account to continue.</p>
            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email or Username</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required />
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                        <a href="{{route('admin.forget.password.get')}}" class="m-auth-link">Forgot Password?</a>
                    </div>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>
                <div class="mb-3 mt-4">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
