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
    /**
     * @var Ranyuen\Navigation
     * @Inject
     */
    protected $nav;
    /**
     * @var Ranyuen\BgImage
     * @Inject
     */
    protected $bgimage;

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

    /**
     * @param string $lang Current lang.
     * @param string $path Template path.
     *
     * @return array
     */
    protected function getDefaultParams($lang, $path)
    {
        if (isset($this->config['lang'][$lang])) {
            $lang = $this->config['lang'][$lang];
        }
        $nav = $this->nav;

        return [
            'lang'       => $lang,
            'nav'        => [
                'global' => $nav->getGlobalNav($lang),
                'local'  => $nav->getLocalNav($lang, $path),
            ],
            'breadcrumb' => $nav->getBreadcrumb($lang, $path),
            'link'       => $nav->getAlterNav($lang, $path),
            'bgimage'    => $this->bgimage->getRandom(),
            'messages'   => $this->config['message'][$lang],
        ];
    }
}
