<?php
namespace Ranyuen\Controller;

use \Ranyuen\App;
use \Ranyuen\BgImage;
use \Ranyuen\Renderer;

class NavController extends Controller
{
    /** @var array */
    private $_config;
    /** @var \Ranyuen\Logger */
    private $_logger;
    /** @var \Ranyuen\Router */
    private $_router;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $c = $app->getContainer();
        $this->_config = $c['config'];
        $this->_logger = $c['logger'];
        $this->_router = $c['router'];
        $this->_nav = $c['nav'];
    }

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
