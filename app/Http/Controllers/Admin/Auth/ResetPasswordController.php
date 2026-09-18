<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class ResetPasswordController extends Controller
{
    public function showForm($token)
    {
        try {
            $record = DB::table('password_resets')->where('token', $token)->first();

            if (!$record) {
                return redirect()->route('admin.login')->with('error', 'Invalid reset token.');
            }

            return view('admin.auth.reset-password', [
                'token' => $token,
                'email' => $record->email,
            ]);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
            ]);

            $record = DB::table('password_resets')->where([
                'email' => $request->email,
                'token' => $request->token,
            ])->first();

            if (!$record) {
                return back()->withInput()->with('error', 'Invalid token!');
            }

            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_resets')->where('email', $request->email)->delete();

            return redirect()->route('admin.login')->with('success', 'Your password has been changed successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
