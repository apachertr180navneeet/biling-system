<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BranchScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->branch_id && !session('active_branch_id')) {
            session(['active_branch_id' => $user->branch_id]);
        }

        return $next($request);
    }
}
