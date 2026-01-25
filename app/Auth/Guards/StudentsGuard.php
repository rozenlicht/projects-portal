<?php

namespace App\Auth\Guards;

use App\Auth\Providers\StudentsProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;

class StudentsGuard implements Guard
{
    protected UserProvider $provider;
    protected Session $session;
    protected ?Authenticatable $user = null;
    protected string $sessionKey = 'students_user';

    public function __construct(UserProvider $provider, Session $session)
    {
        $this->provider = $provider;
        $this->session = $session;
    }

    /**
     * Determine if the current user is authenticated.
     */
    public function check(): bool
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $userData = $this->session->get($this->sessionKey);

        if (!$userData || !isset($userData['persistent_id'])) {
            return null;
        }

        $this->user = $this->provider->retrieveById($userData['persistent_id']);

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): ?string
    {
        $user = $this->user();

        return $user ? $user->getAuthIdentifier() : null;
    }

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool
    {
        // For SAML, validation happens via the SAML response
        // This method is not typically used
        return false;
    }

    /**
     * Set the current user.
     */
    public function setUser(Authenticatable $user): void
    {
        $this->user = $user;

        if ($this->provider instanceof StudentsProvider) {
            $this->provider->storeUser($user);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(): void
    {
        $this->user = null;

        if ($this->provider instanceof StudentsProvider) {
            $this->provider->forgetUser();
        }
    }
}
