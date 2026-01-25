<?php

namespace App\Helpers;

class SamlHelper
{
    /**
     * Check if SAML is enabled by verifying required environment variables are set
     */
    public static function isEnabled(): bool
    {
        return !empty(env('SURF_ENTITY_ID')) 
            && !empty(env('SAML_SP_ENTITY_ID'))
            && !empty(env('SAML_SP_ACS_URL'));
    }
}
