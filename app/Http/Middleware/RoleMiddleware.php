<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $usuario = $request->user();

        if (
            !$usuario ||
            !in_array(
                strtolower($usuario->rol),
                array_map('strtolower', $roles)
            )
        ) {
            abort(403);
        }

        return $next($request);
    }
}
