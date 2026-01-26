<?php

namespace App\Http\Middleware;

use App\Helpers\SamlHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToSamlLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SamlHelper::isEnabled()) {
            return $next($request);
        }

        if (!auth('students')->check()) {
            return redirect('/saml/login?guard=students&return=' . urlencode($request->fullUrl()));
        }

        return $next($request);
    }
}
