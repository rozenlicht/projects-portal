<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;

class StudentsUser implements Authenticatable, Arrayable
{
    protected string $persistentId;
    protected ?string $eduAffiliation;
    protected ?string $email;
    protected string $rememberToken;

    public function __construct(string $persistentId, ?string $eduAffiliation = null, ?string $email = null)
    {
        $this->persistentId = $persistentId;
        $this->eduAffiliation = $eduAffiliation;
        $this->email = $email;
        $this->rememberToken = '';
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'persistent_id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier(): mixed
    {
        return $this->persistentId;
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        // Not used for SAML authentication
        return '';
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): string
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Get the persistent ID.
     */
    public function getPersistentId(): string
    {
        return $this->persistentId;
    }

    /**
     * Get the edu affiliation.
     */
    public function getEduAffiliation(): ?string
    {
        return $this->eduAffiliation;
    }

    /**
     * Get the email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'persistent_id' => $this->persistentId,
            'edu_affiliation' => $this->eduAffiliation,
            'email' => $this->email,
        ];
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['persistent_id'] ?? '',
            $data['edu_affiliation'] ?? null,
            $data['email'] ?? null
        );
    }
}
