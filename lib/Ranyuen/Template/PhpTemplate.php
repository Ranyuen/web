<?php
namespace Ranyuen\Template;

use ReflectionClass;
use ReflectionMethod;

/**
 * PHP as view template engine.
 *
 * cf. harrydeluxe/php-liquid
 */
class PhpTemplate implements Template
{
    /** @var string */
    private $_template;
    /** @var array */
    private $_helpers = [];

    public function registerHelper($helper)
    {
        $this->_helpers[] = $helper;
    }

    public function parse($template)
    {
        $this->_template = $template;

        return $this;
    }

    public function render(array $__params = [], $tmp_helpers = null)
    {
        $helpers = $this->_helpers;
        if (!is_null($tmp_helpers)) {
            if (is_array($tmp_helpers)) {
                array_merge($helpers, $tmp_helpers);
            } else {
                $helpers[] = $tmp_helpers;
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
                ${$__k} = $__v;
            }
            unset($__k);
            unset($__v);
            ob_start();
            eval('?>' . func_get_arg(0));

            return ob_get_clean();
        };
        $render = $render->bindTo(null);

        return $render($this->_template, $__params);
    }
}
