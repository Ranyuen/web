<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;

/**
 * Controller interface.
 */
abstract class Controller
{
    /** @Inject */
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
