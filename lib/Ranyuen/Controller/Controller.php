<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

/**
 * Controller interface
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

    /**
     * @param string $lang
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
