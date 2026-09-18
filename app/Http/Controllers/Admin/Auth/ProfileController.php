<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Exception;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('admin.auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|min:9|unique:users,phone,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'avatar' => 'sometimes|image|mimes:jpeg,jpg,png|max:5000',
            ]);

            if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/user/';
                $path = public_path($folder);

                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $file->move($path, $filename);
                $user->avatar = $folder . $filename;
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name . ' ' . $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
