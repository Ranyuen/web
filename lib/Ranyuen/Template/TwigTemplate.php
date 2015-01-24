<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Template;

use ReflectionClass;
use ReflectionMethod;
use Twig_Loader_Array;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_SimpleFilter;

/**
 * Twig template engine.
 */
class TwigTemplate implements Template
{
    /** @var Twig_Loader_Array */
    private $loader;
    /** @var Twig_Environment */
    private $engine;

    public function __construct($path = null)
    {
        $this->loader = new Twig_Loader_Array(['current' => '']);
        $loader = new Twig_Loader_Chain([$this->loader, new Twig_Loader_Filesystem($path)]);
        $this->engine = new Twig_Environment($loader);
    }

    /**
     * Register the helper.
     *
     * @param mixed $helper Template helper
     *
     * @return void
     */
    public function registerHelper($helper)
    {
        $class = new ReflectionClass(get_class($helper));
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $filter = new Twig_SimpleFilter(
                $method->getName(),
                function () use ($helper, $method) {
                    return $method->invokeArgs($helper, func_get_args());
                }
            );
            $this->engine->addFilter($filter);
        }
    }

    /**
     * Parses the given source string.
     *
     * @param string $template Template string
     *
     * @return Template
     */
    public function parse($template)
    {
        $this->loader->setTemplate('current', $template);

        return $this;
    }

    /**
     * Renders the current template.
     *
     * @param array $params     Template params
     * @param mixed $tmpHelpers Temporary helpers
     *
     * @return string
     */
    public function render(array $params = [], $tmpHelpers = null)
    {
        if ($tmpHelpers) {
            $this->registerHelper($tmpHelpers);
        }

        return $this->engine->render('current', $params);
    }
}
