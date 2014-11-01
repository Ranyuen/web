<?php
/**
 * Static pages
 */
namespace Ranyuen\Controller;

use Ranyuen\BgImage;
use Ranyuen\Helper\Helper;
use Ranyuen\Renderer;

/**
 * Static pages
 */
class NavController extends Controller
{
    /**
     * @Inject
     * @var array
     */
    protected $config;
    /**
     * @Inject
     * @var \Ranyuen\Logger
     */
    protected $logger;
    /**
     * @Inject
     * @var \Ranyuen\Router
     */
    protected $router;
    /**
     * @Inject
     * @var \Ranyuen\Navigation
     */
    protected $nav;

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
                'bgimage'    => (new BgImage())->getRandom(),
                'messages'   => $this->config['message'][$lang],
            ]
        );
        $str = (new Renderer($this->config['templates.path']))
            ->setLayout($this->config['layout'])
            ->addHelper(new Helper($this->config))
            ->render("$templateName.$lang", $params);
        if (false === $str) {
            $this->_router->notFound();
        } else {
            echo $str;
        }
    }
}
