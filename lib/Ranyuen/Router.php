<?php
/**
 * URI router.
 */
namespace Ranyuen;

use Ranyuen\Di\Container;
use ReflectionClass;
use Slim;

/**
 * URI router.
 */
class Router extends Slim\Slim
{
    /** @var App */
    private $app;

    /**
     * @param App   $app    Application
     * @param array $config Application config
     */
    public function __construct(App $app, array $config)
    {
        parent::__construct();
        $this->config($config);
        $this->app = $app;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->app->getContainer();
    }

    /**
     * Override.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function run()
    {
        $router = $this;
        include_once 'config/routes.php';
        $methods = (new ReflectionClass(get_class($this)))->getMethods();
        foreach ($methods as $method) {
            if (preg_match('/@routing/', $method->getDocComment()) >= 1) {
                $method->setAccessible(true);
                $method->invoke($this, $this->app);
            }
        }
        parent::run();
    }
}
