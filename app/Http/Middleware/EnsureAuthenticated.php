<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Redirect ke halaman login jika user belum terautentikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('auth_sinta_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
