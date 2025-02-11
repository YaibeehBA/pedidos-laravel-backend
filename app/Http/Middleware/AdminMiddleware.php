<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado y si es admin
        if ($request->user() && $request->user()->esadmin == 1) {
            return $next($request);
        }

        // Si no es admin, devolver un error 403
        return response()->json([
            'message' => 'No autorizado'
        ], 403);
    }
}