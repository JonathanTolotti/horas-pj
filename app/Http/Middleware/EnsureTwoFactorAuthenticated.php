<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && session('two_factor_pending')) {
            $exempt = $request->routeIs('two-factor.*')
                || $request->routeIs('logout')
                || $request->routeIs('verification.*');

            if (!$exempt) {
                return redirect()->route('two-factor.show');
            }
        }

        return $next($request);
    }
}
