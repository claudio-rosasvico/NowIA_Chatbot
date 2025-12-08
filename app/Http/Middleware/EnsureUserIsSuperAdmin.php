<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_super_admin) {
            abort(403, 'Acceso denegado. Se requiere nivel de Desarrollador.');
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta ha sido desactivada. Impacte el pago para continuar.']);
        }

        return $next($request);
    }
}
