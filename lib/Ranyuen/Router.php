<?php
namespace Ranyuen;

use ReflectionClass;
use Slim;

class Router extends Slim\Slim
{
    /** @var App */
    private $_app;

    /**
     * @param App        $app
     * @param Navigation $nav
     * @param Logger     $logger
     * @param array      $config
     */
    public function __construct(App $app, array $config)
    {
        parent::__construct();
        $this->config($config);
        $this->_app = $app;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->_app->getContainer();
    }

    /**
     * Override.
     */
    public function run()
    {
        $router = $this;
        require_once 'config/routes.php';
        $methods = (new ReflectionClass(get_class($this)))->getMethods();
        foreach ($methods as $method) {
            if (preg_match('/@routing/', $method->getDocComment()) >= 1) {
                $method->setAccessible(true);
                $method->invoke($this, $this->_app);
            }
        }
        parent::run();
    }
}
