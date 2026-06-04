<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestRestrictionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.dashboard' : 'student.dashboard');
        }

        return $next($request);
    }
}
