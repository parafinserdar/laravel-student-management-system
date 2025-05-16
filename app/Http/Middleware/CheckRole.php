<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Yetkisiz'], 401);
        }

        $userRole = $request->user()->role->slug;

        if (!in_array($userRole, $roles)) {
            return response()->json(['error' => 'Bu işlem için yetkiniz yok'], 403);
        }

        return $next($request);
    }
}
