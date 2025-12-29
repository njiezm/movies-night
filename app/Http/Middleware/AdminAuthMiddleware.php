<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Session déjà active
        if (session('admin_authenticated')) {
            return $next($request);
        }

        // Tentative de reconnexion via cookie "remember me"
        if ($request->hasCookie('admin_remember')) {

            $level = $request->cookie('admin_remember');

            if (in_array($level, ['full', 'limited'])) {

                session([
                    'admin_authenticated' => true,
                    'show_dotations' => $level === 'full'
                ]);

                return $next($request);
            }
        }

        // Pas connecté
        return redirect()->route('admin.login.form');
    }
}
