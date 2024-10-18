<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (isAdminUri() && Auth::check()) {
            $user = Auth::user();

            try {
                $dashboardAccess = $user->hasPermissionTo('dashboard.access');
            } catch (\Exception) {
                $dashboardAccess = false;
            }

            if (isAdminUri() && !$dashboardAccess && !in_array(Route::currentRouteName(), ['admin.login', 'admin.logout'])) {
                throw new UnauthorizedException(403);
            }
        }

        return $next($request);
    }
}
