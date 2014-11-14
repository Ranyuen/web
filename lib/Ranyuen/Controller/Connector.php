<?php
/**
 * Ranyuen web site.
 */
namespace Ranyuen\Controller;

use Ginq;

/**
 * Connect to controllers.
 */
class Connector
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $class  Controller name.
     * @param string $method Method name.
     * @param array  $params Params map.
     *
     * @return mixed
     *
     * @throws \ReflectionException No such class and method.
     */
    public function invoke($class, $method, $params = [])
    {
        $class = 'Ranyuen\Controller\\'.$class.'Controller';
        $controller = $this->container->newInstance($class);
        $invocation = [$controller, $method];
        $method = new \ReflectionMethod($class, $method);
        foreach ($method->getParameters() as $param) {
            if (!isset($params[$param->getName()])) {
                if (!$param->isOptional()) {
                    $params[] = null;
                }
            } else {
                $params[] = $params[$param->getName()];
            }
        }

        return call_user_func_array($invocation, $params);
    }

    // public function route()
    // {
    //     $controllers = Ginq::from(get_declared_classes())
    //         ->where(function ($c) { return preg_match('/^Ranyuen\Controller\.+Controller$/', $c); })
    //         ->select(function ($c) { return new \ReflectionClass($c); })
    //         ->where(function ($c) { return !$c->isAbstract() && !$c->isInterface(); });
    //     foreach ($controllers as $controller) {
    //         $methods = $controller->getMethods(
    //             ReflectionMethod::IS_PUBLIC
    //             & !ReflectionMethod::IS_STATIC
    //         );
    //         foreach ($methods as $method) {
    //             $routes = (new RouteAnnotation())->getRoutes($method);
    //             foreach ($routes as $route) {
    //                 $this->router->map(
    //                     $route[1],
    //                     function () {
    //                     }
    //                 )->via([$route[0]]);
    //             }
    //         }
    //     }
    // }
}
