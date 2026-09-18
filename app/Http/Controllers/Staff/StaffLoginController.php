<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('staff.dashboard');
        }
        return view('staff.auth.login');
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

                $allowedRoles = ['super-admin', 'admin', 'manager', 'staff'];
                if ($user->hasRole($allowedRoles)) {
                    return redirect()->route('staff.dashboard')->with('success', 'Welcome back, ' . $user->first_name . '!');
                }

                Auth::logout();
                return back()->with('error', 'You do not have access to the staff portal.');
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
            return redirect()->route('staff.login')->with('success', 'Logged out successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
