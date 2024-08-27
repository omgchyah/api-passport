<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            return response()->json([
                "message" => "Unauthorized."
            ], 403);
        }
        return $next($request);
    }
}
