<?php

namespace App\Auth\Providers;

use App\Auth\StudentsUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;

class StudentsProvider implements UserProvider
{
    protected Session $session;
    protected string $sessionKey = 'students_user';

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        $userData = $this->session->get($this->sessionKey);

        if (!$userData || ($userData['persistent_id'] ?? null) !== $identifier) {
            return null;
        }

        return StudentsUser::fromArray($userData);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $user = $this->retrieveById($identifier);

        if (!$user || $user->getRememberToken() !== $token) {
            return null;
        }

        return $user;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $userData = $this->session->get($this->sessionKey, []);

        if (isset($userData['persistent_id']) && $userData['persistent_id'] === $user->getAuthIdentifier()) {
            $userData['remember_token'] = $token;
            $this->session->put($this->sessionKey, $userData);
        }
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        // For SAML, we don't use credentials - user is authenticated via SAML response
        // This method is called but we'll return the user from session
        return $this->retrieveById($credentials['persistent_id'] ?? null);
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        // SAML authentication is validated by the SAML response itself
        // This method is not used for SAML authentication
        return true;
    }

    /**
     * Rehash the user's password if required and supported.
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // SAML authentication doesn't use passwords, so this is a no-op
        // This method is required by the UserProvider interface in Laravel 12+
    }

    /**
     * Store user data in session.
     */
    public function storeUser(StudentsUser $user): void
    {
        $this->session->put($this->sessionKey, $user->toArray());
    }

    /**
     * Remove user data from session.
     */
    public function forgetUser(): void
    {
        $this->session->forget($this->sessionKey);
    }
}
