<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Uso:
     *  ->middleware('role:admin')
     *  ->middleware('role:admin,vendedor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'No autenticado.');
        }

        $userRole = strtolower(trim((string) $user->role));
        $allowed = array_map(fn ($r) => strtolower(trim($r)), $roles);

        if (! in_array($userRole, $allowed, true)) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
