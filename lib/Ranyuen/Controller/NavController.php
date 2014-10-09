<?php
namespace Ranyuen\Controller;

use \Ranyuen\BgImage;
use \Ranyuen\Renderer;

class NavController extends Controller
{
    /**
     * @Inject
     * @Named("_config=config")
     * @var array
     */
    private $_config;
    /**
     * @Inject
     * @var \Ranyuen\Logger
     */
    private $_logger;
    /**
     * @Inject
     * @var \Ranyuen\Router
     */
    private $_router;
    /**
     * @Inject
     * @var \Ranyuen\Navigation
     */
    private $_nav;

    /**
     * @param string $lang
     * @param array  $path
     */
    public function showFromTemplate($lang, $path)
    {
        foreach ($this->_config['redirect'] as $src => $dest) {
            if ($_SERVER['REQUEST_URI'] === $src) {
                $this->_router->redirect($dest, 301);
            }
        }
        $this->render($lang, $path);
        $this->_logger->addAccessInfo();
    }

    /**
     * @param string $lang
     * @param string $template_name
     * @param array  $params
     */
    private function render($lang, $template_name, $params = [])
    {
        $renderer = new Renderer($this->_config);
        if (isset($this->_config['lang'][$lang])) {
            $lang = $this->_config['lang'][$lang];
        }
        $params['lang'] = $lang;
        $params['global_nav'] = $this->_nav->getGlobalNav($lang);
        $params['local_nav'] = $this->_nav->getLocalNav($lang, $template_name);
        $params['news_nav'] = $this->_nav->getNews($lang);
        $params['breadcrumb'] = $this->_nav->getBreadcrumb($lang, $template_name);
        $params['link'] = $this->_nav->getAlterNav($lang, $template_name);
        $params['bgimage'] = (new BgImage())->getRandom();
        $str = $renderer
            ->setLayout($this->_config['layout'])
            ->render("$template_name.$lang", $params);
        if ($str === false) {
            $this->_router->notFound();
        } else {
            echo $str;
        }
    }
}
