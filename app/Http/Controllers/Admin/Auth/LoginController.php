<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->with('error', 'Invalid credentials.');
            }

            if ($user->status !== 'active') {
                return back()->with('error', 'Your account has been deactivated.');
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard')->with('success', 'Welcome to your dashboard.');
                }

                Auth::logout();
                return back()->with('error', 'You do not have access to the admin panel.');
            }

            return back()->with('error', 'Invalid credentials.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
