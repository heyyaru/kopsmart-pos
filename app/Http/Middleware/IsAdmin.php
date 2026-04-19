<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // LOGIKA: Jika user BUKAN admin, tendang keluar (Error 403)
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Admin.');
        }
        return $next($request);
    }
}
