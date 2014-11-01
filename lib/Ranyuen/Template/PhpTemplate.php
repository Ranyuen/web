<?php
/**
 * PHP as view template engine.
 */
namespace Ranyuen\Template;

use ReflectionClass;
use ReflectionMethod;

/**
 * PHP as view template engine.
 */
class PhpTemplate implements Template
{
    /** @var string */
    private $template;
    /** @var array */
    private $helpers = [];

    public function registerHelper($helper)
    {
        $this->helpers[] = $helper;
    }

    public function parse($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param array $__params   Template params
     * @param mixed $tmpHelpers Temporary helpers
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function render(array $__params = [], $tmpHelpers = null)
    {
        $helpers = $this->helpers;
        if (!is_null($tmpHelpers)) {
            if (is_array($tmpHelpers)) {
                $helpers = array_merge($helpers, $tmpHelpers);
            } else {
                $helpers[] = $tmpHelpers;
            }
        }
        foreach ($helpers as $__helper) {
            $class = new ReflectionClass(get_class($__helper));
            $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $__method) {
                $__params[$__method->getName()] = function () use ($__helper, $__method) {
                    return $__method->invokeArgs($__helper, func_get_args());
                };
            }
        }
        $render = function () use ($__params) {
            foreach (func_get_arg(1) as $__k => $__v) {
                ${$__k}
                = $__v;
            }
            unset($__k);
            unset($__v);
            ob_start();
            eval('?>'.func_get_arg(0));

            return ob_get_clean();
        };
        $render = $render->bindTo(null);

        return $render($this->template, $__params);
    }
}
