<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

/**
 * Static pages
 */
class NavController extends Controller
{
    /**
     * @var Ranyuen\Logger
     * @Inject
     */
    protected $logger;
    /**
     * @var Ranyuen\Router
     * @Inject
     */
    protected $router;
    /**
     * @var Ranyuen\Renderer
     * @Inject
     */
    protected $renderer;

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
        $params = array_merge(
            $params,
            $this->getDefaultParams($lang, $templateName)
        );
        $lang = $params['lang'];
        $str = $this->renderer->render("$templateName.$lang", $params);
        if (false === $str) {
            $this->router->notFound();
        } else {
            echo $str;
        }
    }
}
