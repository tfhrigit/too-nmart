<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareUserPermissions
{
    /**
     * Share user permissions with all views.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $permissions = $request->user()->getPermissions();
            view()->share('userPermissions', $permissions);
            view()->share('userRole', $request->user()->role);
        }

        return $next($request);
    }
}