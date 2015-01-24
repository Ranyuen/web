<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

/**
 * HTTP 403 Forbidden error.
 */
class Http403ForbiddenException extends \Exception
{
    /** @var string */
    public $redirectUri;

    /**
     * @param string $redirectUri Redirect URI (Not auto redirect).
     */
    public function __construct($redirectUri = null)
    {
        parent::__construct();
        $this->redirectUri = $redirectUri;
    }
}
