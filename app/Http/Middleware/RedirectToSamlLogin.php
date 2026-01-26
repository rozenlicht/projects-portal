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

        // Don't redirect SAML routes themselves or onboarding routes
        if ($request->is('saml/*') || $request->is('onboarding/*')) {
            return $next($request);
        }

        // Don't redirect if already on the login page (prevent loops)
        if ($request->is('saml/login')) {
            return $next($request);
        }

        if (!auth('students')->check()) {
            $returnUrl = $request->fullUrl();
            // Don't redirect to login if we're already being redirected from login
            if (str_contains($returnUrl, 'saml/login')) {
                $returnUrl = '/';
            }
            return redirect('/saml/login?guard=students&return=' . urlencode($returnUrl));
        }

        return $next($request);
    }
}
