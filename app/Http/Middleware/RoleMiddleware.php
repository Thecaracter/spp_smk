<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        if ($role === 'admin' && auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required.');
        }

        if ($role === 'mahasiswa' && auth()->user()->role !== 'mahasiswa') {
            abort(403, 'Mahasiswa access required.');
        }

        return $next($request);
    }
}
