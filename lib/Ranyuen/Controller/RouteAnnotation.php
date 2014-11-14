<?php
/**
 * Ranyuen web site.
 */
namespace Ranyuen\Controller;

use Ranyuen\Di\Annotation;

/**
 * Controller route annotation.
 */
class RouteAnnotation extends Annotation
{
    /**
     * @param \ReflectionMethod $method Target method.
     *
     * @return array
     */
    public function getRoutes($method)
    {
        return array_map(
            function ($route) {
                list($method, $path) = explode(' ', $route);

                return [strtoupper($method), $path];
            },
            $this->getValues($method, 'Route')
        );
    }
}
