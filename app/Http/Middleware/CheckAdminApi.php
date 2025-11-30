<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminApi
{
    public function handle(Request $request, Closure $next)
{
    $user = $request->user();
    if (!$user || (strtolower($user->role()->code) !== 'admin' && strtolower($user->role()->code) !== 'super_admin')) {
        return response()->json([
            'message' => 'Access denied: Admin only'
        ], 403);
    }

    return $next($request);
}

}
