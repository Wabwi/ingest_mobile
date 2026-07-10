<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RedirectToMobileSetup
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasUsers = User::exists();
        $isSetupRoute = $request->routeIs('mobile-setup') || $request->routeIs('mobile-setup.submit');

        if (!$hasUsers && !$isSetupRoute) {
            return redirect()->route('mobile-setup');
        }

        if ($hasUsers && $isSetupRoute) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
