<?php
namespace Ranyuen\Template;

use ReflectionClass;
use ReflectionMethod;
use Twig_Loader_Array;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_SimpleFilter;

class TwigTemplate implements Template
{
    /** @var Twig_Loader_Array */
    private $_loader;
    /** @var Twig_Environment */
    private $_engine;

    public function __construct($path = null)
    {
        $this->_loader = new Twig_Loader_Array(['current' => '']);
        $loader = new Twig_Loader_Chain([
            $this->_loader,
            new Twig_Loader_Filesystem($path),
        ]);
        $this->_engine = new Twig_Environment($loader);
    }

    public function registerHelper($helper)
    {
        $class = new ReflectionClass(get_class($helper));
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $filter = new Twig_SimpleFilter($method->getName(), function () use ($helper, $method) {
                return $method->invokeArgs($helper, func_get_args());
            });
            $this->_engine->addFilter($filter);
        }
    }

    public function parse($template)
    {
        $this->_loader->setTemplate('current', $template);

        return $this;
    }

    public function render(array $__params = [], $tmp_helpers = null)
    {
        if ($tmp_helpers) {
            $this->registerHelper($tmp_helpers);
        }

        return $this->_engine->render('current', $__params);
    }
}
