<?php

namespace App\Http\Controllers\Auth;

use App\Auth\StudentsUser;
use App\Helpers\SamlHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OneLogin\Saml2\Auth as SamlAuth;
use OneLogin\Saml2\Settings;
use OneLogin\Saml2\Utils;

class SamlController extends Controller
{
    /**
     * Check if SAML is enabled, abort if not
     */
    protected function ensureSamlEnabled(): void
    {
        if (!SamlHelper::isEnabled()) {
            abort(503, 'SAML authentication is not configured. Please set the required environment variables.');
        }
    }
    protected function getSamlAuth(?string $guard = null): SamlAuth
    {
        $settings = $this->getSamlSettings();
        return new SamlAuth($settings);
    }

    protected function getSamlSettings(): array
    {
        $config = config('saml.settings');

        // Load certificates from files if paths are provided
        if (empty($config['sp']['x509cert']) && !empty(config('saml.sp.public_cert_path'))) {
            $certPath = config('saml.sp.public_cert_path');
            if (file_exists($certPath)) {
                $config['sp']['x509cert'] = file_get_contents($certPath);
            }
        }

        if (empty($config['sp']['privateKey']) && !empty(config('saml.sp.private_key_path'))) {
            $keyPath = config('saml.sp.private_key_path');
            if (file_exists($keyPath)) {
                $config['sp']['privateKey'] = file_get_contents($keyPath);
            }
        }

        if (empty($config['idp']['x509cert']) && !empty(config('saml.surf.public_cert_path'))) {
            $certPath = config('saml.surf.public_cert_path');
            if (file_exists($certPath)) {
                $config['idp']['x509cert'] = file_get_contents($certPath);
            }
        }

        return $config;
    }

    /**
     * Initiate SAML SSO login
     */
    public function login(Request $request)
    {
        $this->ensureSamlEnabled();
        
        $guard = $request->get('guard', 'students');
        $returnUrl = $request->get('return', '/');

        // Store return URL and guard in session
        session(['saml_return_url' => $returnUrl, 'saml_guard' => $guard]);

        try {
            $samlAuth = $this->getSamlAuth($guard);
            $samlAuth->login($returnUrl);
        } catch (\Exception $e) {
            Log::error('SAML login error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Handle SAML response (Assertion Consumer Service)
     */
    public function acs(Request $request)
    {
        $this->ensureSamlEnabled();
        
        $guard = session('saml_guard', 'students');
        $returnUrl = session('saml_return_url', '/');

        try {
            $samlAuth = $this->getSamlAuth($guard);
            $samlAuth->processResponse();

            $errors = $samlAuth->getErrors();

            if (!empty($errors)) {
                Log::error('SAML ACS errors: ' . implode(', ', $errors));
                return redirect('/')->with('error', 'Authentication failed. Please try again.');
            }

            if (!$samlAuth->isAuthenticated()) {
                return redirect('/')->with('error', 'Authentication failed. Please try again.');
            }

            // Extract attributes
            $attributes = $samlAuth->getAttributes();
            $nameId = $samlAuth->getNameId();

            // Map attributes
            $persistentId = $this->extractAttribute($attributes, 'persistent_id') ?? $nameId;
            $email = $this->extractAttribute($attributes, 'email');
            $eduAffiliation = $this->extractAttribute($attributes, 'edu_affiliation');

            if (empty($persistentId)) {
                Log::error('SAML: No persistent ID found in response');
                return redirect('/')->with('error', 'Authentication failed: Missing user identifier.');
            }

            // Handle authentication based on guard
            if ($guard === 'students') {
                return $this->handleStudentsAuth($persistentId, $eduAffiliation, $email, $returnUrl);
            } else {
                return $this->handleAdminAuth($persistentId, $email, $returnUrl);
            }
        } catch (\Exception $e) {
            Log::error('SAML ACS error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Handle students guard authentication (anonymous, session-based)
     */
    protected function handleStudentsAuth(string $persistentId, ?string $eduAffiliation, ?string $email, string $returnUrl)
    {
        $user = new StudentsUser($persistentId, $eduAffiliation, $email);
        Auth::guard('students')->setUser($user);

        // Clear SAML session data
        session()->forget(['saml_return_url', 'saml_guard']);

        return redirect($returnUrl);
    }

    /**
     * Handle admin guard authentication (database lookup by email)
     */
    protected function handleAdminAuth(string $persistentId, ?string $email, string $returnUrl)
    {
        if (empty($email)) {
            Log::error('SAML Admin: No email found in response');
            return redirect('/admin/login')->with('error', 'Authentication failed: Email address required for admin access.');
        }

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning('SAML Admin: User not found with email: ' . $email);
            return redirect('/admin/login')->with('error', 'No account found with this email address.');
        }

        // Check if user has access to admin panel
        $panel = \Filament\Facades\Filament::getPanel('admin');
        if ($panel && !$user->canAccessPanel($panel)) {
            return redirect('/admin/login')->with('error', 'You do not have access to the admin panel.');
        }

        // Store persistent ID if not already stored
        if (empty($user->surf_id)) {
            $user->surf_id = $persistentId;
            $user->save();
        }

        // Authenticate user
        Auth::guard('web')->login($user);

        // Clear SAML session data
        session()->forget(['saml_return_url', 'saml_guard']);

        return redirect($returnUrl ?: '/admin');
    }

    /**
     * Initiate SAML logout
     */
    public function logout(Request $request)
    {
        $this->ensureSamlEnabled();
        
        $guard = $request->get('guard', Auth::getDefaultDriver());

        try {
            $samlAuth = $this->getSamlAuth($guard);

            // Logout from local session first
            if ($guard === 'students') {
                Auth::guard('students')->logout();
            } else {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Initiate SAML logout
            $returnTo = $request->get('return', url('/'));
            $samlAuth->logout($returnTo);
        } catch (\Exception $e) {
            Log::error('SAML logout error: ' . $e->getMessage());

            // Fallback to local logout
            if ($guard === 'students') {
                Auth::guard('students')->logout();
            } else {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/');
        }
    }

    /**
     * Handle SAML Single Logout Service callback
     */
    public function sls(Request $request)
    {
        $this->ensureSamlEnabled();
        
        try {
            $samlAuth = $this->getSamlAuth();
            $samlAuth->processSLO();

            $errors = $samlAuth->getErrors();

            if (!empty($errors)) {
                Log::error('SAML SLS errors: ' . implode(', ', $errors));
            }

            // Logout from local session
            Auth::guard('students')->logout();
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Exception $e) {
            Log::error('SAML SLS error: ' . $e->getMessage());

            // Fallback logout
            Auth::guard('students')->logout();
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }

    /**
     * Generate and return SP metadata
     */
    public function metadata()
    {
        $this->ensureSamlEnabled();
        
        try {
            $settings = new Settings($this->getSamlSettings(), true);
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);

            if (!empty($errors)) {
                Log::error('SAML metadata errors: ' . implode(', ', $errors));
                return response('Metadata validation failed', 500);
            }

            return response($metadata, 200, [
                'Content-Type' => 'application/xml',
            ]);
        } catch (\Exception $e) {
            Log::error('SAML metadata error: ' . $e->getMessage());
            return response('Metadata generation failed', 500);
        }
    }

    /**
     * Extract attribute value from SAML attributes array
     */
    protected function extractAttribute(array $attributes, string $key): ?string
    {
        $mappings = config("saml.attributes.{$key}", []);

        foreach ($mappings as $attributeName) {
            if (isset($attributes[$attributeName])) {
                $value = $attributes[$attributeName];
                // SAML attributes can be arrays, get first value
                if (is_array($value) && !empty($value)) {
                    return $value[0];
                }
                if (is_string($value)) {
                    return $value;
                }
            }
        }

        return null;
    }
}
