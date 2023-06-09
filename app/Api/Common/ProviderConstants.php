<?php

namespace App\Api\Common;

/**
 * Provider constants
 * Basically used for URLs
 */

class ProviderConstants
{
    public const BASE_LP_EXPRESS_API_URL = 'https://api-manosiuntostst.post.lt/oauth/token';
    public const LP_EXPRESS_API_SCOPE = 'read+write';
    public const LP_EXPRESS_API_GRANT_TYPE_PASSWORD = 'password';
    public const LP_EXPRESS_API_GRANT_TYPE_REFRESH = 'refresh_token';
    public const LP_EXPRESS_API_CLIENT_SYSTEM = 'PUBLIC';
    public const BASE_OMNIVA_API_URL = 'https://edixml.post.ee';
}
