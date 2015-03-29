<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;

/**
 * Controller interface.
 */
abstract class Controller
{
    /**
     * Config.
     *
     * @Inject
     */
    protected $config;

    protected function toJsonResponse($res, $statusCode = null, $headers = [])
    {
        if (is_array($res)) {
            $res = json_encode($res);
        }
        if (!($res instanceof Response)) {
            $res = new Response((string) $res);
        }
        if (!is_null($statusCode)) {
            $res->setStatusCode($statusCode);
        }
        $res->headers->add($headers);
        $res->headers->set('Content-Type', 'application/json');

        return $res;
    }
}
