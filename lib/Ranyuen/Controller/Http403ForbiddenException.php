<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

/**
 * HTTP 403 Forbidden error.
 */
class Http403ForbiddenException extends \Exception
{
    /**
     * Redirect destination.
     *
     * @var string
     */
    public $redirectUri;

    /**
     * Constructor.
     *
     * @param string $redirectUri Redirect URI (Not auto redirect).
     */
    public function __construct($redirectUri = null)
    {
        parent::__construct();
        $this->redirectUri = $redirectUri;
    }
}
