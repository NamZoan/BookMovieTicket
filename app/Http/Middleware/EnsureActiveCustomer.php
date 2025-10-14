<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActiveCustomer
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !$user->isActiveCustomer()) {
            abort(403, 'Only active customers can perform this action.');
        }
        return $next($request);
    }
}