<?php
namespace Ranyuen\Controller;

use Ranyuen\BgImage;
use Ranyuen\Helper\Helper;
use Ranyuen\Renderer;

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
        foreach ($this->_config['redirect'] as $from => $to) {
            if ($_SERVER['REQUEST_URI'] === $from) {
                $this->_router->redirect($to, 301);
            }
        }
        $this->render($lang, $path);
        $this->_logger->addAccessInfo();
    }

    /**
     * Echo rendered string.
     *
     * @param  string $lang
     * @param  string $template_name
     * @param  array  $params
     * @return void
     */
    private function render($lang, $template_name, $params = [])
    {
        if (isset($this->_config['lang'][$lang])) {
            $lang = $this->_config['lang'][$lang];
        }
        $nav = $this->_nav;
        $params = array_merge($params, [
            'lang'       => $lang,
            'nav'        => [
                'global' => $nav->getGlobalNav($lang),
                'local'  => $nav->getLocalNav($lang, $template_name),
                'news'   => $nav->getNews($lang),
            ],
            'breadcrumb' => $nav->getBreadcrumb($lang, $template_name),
            'link'       => $nav->getAlterNav($lang, $template_name),
            'bgimage'    => (new BgImage())->getRandom(),
            'messages'   => $this->_config['message'][$lang],
        ]);
        $str = (new Renderer($this->_config['templates.path']))
            ->setLayout($this->_config['layout'])
            ->addHelper(new Helper($this->_config))
            ->render("$template_name.$lang", $params);
        if ($str === false) {
            $this->_router->notFound();
        } else {
            echo $str;
        }
    }
}
