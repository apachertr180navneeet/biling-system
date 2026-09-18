@extends('web.layouts.app')

@section('title', 'Guest Portal - ' . ($company->name ?? config('app.name')))

@section('style')
<style>
    .guest-login {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #17211d 0%, #14624f 100%);
        padding: 60px 16px;
    }
    .guest-login__card {
        width: 100%;
        max-width: 460px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        overflow: hidden;
    }
    .guest-login__header {
        background: #14624f;
        padding: 40px 40px 30px;
        text-align: center;
        color: #fff;
    }
    .guest-login__icon {
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
    .guest-login__header h2 {
        margin: 0 0 6px;
        font-size: 26px;
        font-weight: 700;
    }
    .guest-login__header p {
        margin: 0;
        opacity: 0.85;
        font-size: 15px;
    }
    .guest-login__body {
        padding: 36px 40px 40px;
    }
    .guest-login__body .form-floating {
        margin-bottom: 20px;
    }
    .guest-login__body .form-floating label {
        color: #6c757d;
    }
    .guest-login__body .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 14px 16px;
        font-size: 16px;
        transition: border-color 0.2s;
    }
    .guest-login__body .form-control:focus {
        border-color: #14624f;
        box-shadow: 0 0 0 0.2rem rgba(20,98,79,0.15);
    }
    .guest-login__body .btn-login {
        width: 100%;
        padding: 14px;
        background: #14624f;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }
    .guest-login__body .btn-login:hover {
        background: #0f4d3e;
    }
    .guest-login__footer {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        margin-top: 8px;
    }
    .guest-login__footer a {
        color: #14624f;
        font-weight: 600;
        font-size: 14px;
    }
    .guest-login__footer a:hover {
        text-decoration: underline;
    }
    .guest-login__help {
        text-align: center;
        margin-top: 16px;
        font-size: 13px;
        color: #6c757d;
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
<section class="guest-login">
    <div class="guest-login__card">
        <div class="guest-login__header">
            <div class="guest-login__icon">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <h2>Guest Portal</h2>
            <p>Access your booking and request services</p>
        </div>
        <div class="guest-login__body">
            @if(session('error'))
            <div class="alert alert-danger" style="background: #fee; color: #c33;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('guest.login.post') }}" method="POST">
                @csrf
                <div class="form-floating">
                    <input type="text" name="reservation_number" class="form-control" id="reservation_number" value="{{ old('reservation_number') }}" placeholder="RES-000001" required>
                    <label for="reservation_number"><i class="fas fa-hashtag me-1"></i> Reservation Number</label>
                </div>
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="your@email.com" required>
                    <label for="email"><i class="fas fa-envelope me-1"></i> Email Address</label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-1"></i> Access My Booking
                </button>
            </form>

            <div class="guest-login__footer">
                <a href="{{ route('web.booking') }}"><i class="fas fa-search me-1"></i> Need to book? Search rooms</a>
            </div>
        </div>
    </div>
    <div class="guest-login__help">
        <p class="mb-0">Use your reservation number from your booking confirmation email.</p>
    </div>
</section>
@endsection
