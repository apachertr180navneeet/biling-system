<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class ChangePasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.change-password');
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|confirmed|min:6',
            ]);

            if (!Hash::check($request->old_password, Auth::user()->password)) {
                return back()->with('error', 'Old password doesn\'t match!');
            }

            User::whereId(Auth::user()->id)->update([
                'password' => Hash::make($request->new_password),
            ]);

            return back()->with('success', 'Password changed successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
