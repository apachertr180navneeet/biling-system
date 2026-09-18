<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
            ]);

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $resetLink = url('admin/reset-password/' . $token);

            Mail::send('admin.email.forgot-password', ['token' => $resetLink, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password');
            });

            return redirect()->route('admin.login')->with('success', 'We have e-mailed your password reset link!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
