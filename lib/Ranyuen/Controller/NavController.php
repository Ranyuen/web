<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\BgImage;

/**
 * Static pages
 */
class NavController extends Controller
{
    /**
     * @var array
     * @Inject
     */
    protected $config;
    /**
     * @var \Ranyuen\Logger
     * @Inject
     */
    protected $logger;
    /**
     * @var \Ranyuen\Router
     * @Inject
     */
    protected $router;
    /**
     * @var \Ranyuen\Navigation
     * @Inject
     */
    protected $nav;
    /**
     * @var \Ranyuen\Renderer
     * @Inject
     */
    protected $renderer;
    /**
     * @var \Ranyuen\BgImage
     * @Inject
     */
    protected $bgimage;

    /**
     * @param string $lang Current lang
     * @param string $path URI path
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function showFromTemplate($lang, $path)
    {
        foreach ($this->config['redirect'] as $from => $to) {
            if ($_SERVER['REQUEST_URI'] === $from) {
                $this->router->redirect($to, 301);
            }
        }
        $this->render($lang, $path);
        $this->logger->addAccessInfo();
    }

    /**
     * Echo rendered string.
     *
     * @param string $lang         Current lang
     * @param string $templateName Template name
     * @param array  $params       Template params
     *
     * @return void
     */
    protected function render($lang, $templateName, $params = [])
    {
        if (isset($this->config['lang'][$lang])) {
            $lang = $this->config['lang'][$lang];
        }
        $nav = $this->nav;
        $params = array_merge(
            $params,
            [
                'lang'       => $lang,
                'nav'        => [
                    'global' => $nav->getGlobalNav($lang),
                    'local'  => $nav->getLocalNav($lang, $templateName),
                    'news'   => $nav->getNews($lang),
                ],
                'breadcrumb' => $nav->getBreadcrumb($lang, $templateName),
                'link'       => $nav->getAlterNav($lang, $templateName),
                'bgimage'    => $this->bgimage->getRandom(),
                'messages'   => $this->config['message'][$lang],
            ]
        );
        $str = $this->renderer->render("$templateName.$lang", $params);
        if (false === $str) {
            $this->router->notFound();
        } else {
            echo $str;
        }
    }
}
