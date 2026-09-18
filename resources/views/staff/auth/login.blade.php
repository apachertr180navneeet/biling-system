@extends('web.layouts.app')

@section('title', 'Staff Login - ' . ($company->name ?? config('app.name')))

@section('style')
<style>
    .staff-login {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #17211d 0%, #14624f 100%);
        padding: 60px 16px;
    }
    .staff-login__card {
        width: 100%;
        max-width: 460px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        overflow: hidden;
    }
    .staff-login__header {
        background: #17211d;
        padding: 40px 40px 30px;
        text-align: center;
        color: #fff;
    }
    .staff-login__icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
    }
    .staff-login__header h2 {
        margin: 0 0 6px;
        font-size: 26px;
        font-weight: 700;
    }
    .staff-login__header p {
        margin: 0;
        opacity: 0.85;
        font-size: 15px;
    }
    .staff-login__body {
        padding: 36px 40px 40px;
    }
    .staff-login__body .guest-field {
        margin-bottom: 20px;
    }
    .staff-login__body .guest-field label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #66716b;
        margin-bottom: 6px;
    }
    .staff-login__body .guest-field input {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-family: inherit;
        font-size: 16px;
        transition: border-color 0.2s;
        outline: none;
    }
    .staff-login__body .guest-field input:focus {
        border-color: #14624f;
        box-shadow: 0 0 0 3px rgba(20,98,79,0.15);
    }
    .staff-login__body .btn-login {
        width: 100%;
        padding: 14px;
        background: #17211d;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }
    .staff-login__body .btn-login:hover {
        background: #14624f;
    }
    .staff-login__footer {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        margin-top: 8px;
    }
    .staff-login__footer a {
        color: #14624f;
        font-weight: 600;
        font-size: 14px;
    }
    .staff-login__footer a:hover {
        text-decoration: underline;
    }
    .alert {
        border-radius: 10px;
        border: none;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<section class="staff-login">
    <div class="staff-login__card">
        <div class="staff-login__header">
            <div class="staff-login__icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h2>Staff Portal</h2>
            <p>Sign in to manage tasks and operations</p>
        </div>
        <div class="staff-login__body">
            @if(session('error'))
            <div class="alert" style="background: #fee; color: #c33;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="alert" style="background: #e8f5e9; color: #14624f;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('staff.login.post') }}" method="POST">
                @csrf
                <div class="guest-field">
                    <label><i class="fas fa-envelope me-1"></i> Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                </div>
                <div class="guest-field">
                    <label><i class="fas fa-lock me-1"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-1"></i> Sign In
                </button>
            </form>

            <div class="staff-login__footer">
                <a href="{{ route('/') }}"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
            </div>
        </div>
    </div>
</section>
@endsection
