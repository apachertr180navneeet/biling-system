<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.get');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login.get')->with('error', 'Your account has been deactivated.');
        }

        if ($user->role === 'user') {
            return $next($request);
        }

        return back()->with('error', 'You do not have access to this area.');
    }
}
