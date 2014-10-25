<?php
namespace Ranyuen\Template;

use Liquid;

defined('LIQUID_INCLUDE_ALLOW_EXT') || define('LIQUID_INCLUDE_ALLOW_EXT', true);

class LiquidTemplate implements Template
{
    /** @var Liquid\Template */
    private $_template;

    public function __construct($path = null)
    {
        $this->_template = new Liquid\Template($path);
    }

    public function registerHelper($helper)
    {
        if (is_array($helper)) {
            foreach ($helper as $name => $class) {
                if (is_string($class)) {
                    $this->_template->registerTag($name, $class);
                } else {
                    $this->_template->registerFilter($class);
                }
            }
        } else {
            $this->_template->registerFilter($helper);
        }
    }

    public function parse($template)
    {
        $this->_template->parse($template);

        return $this;
    }

    public function render(array $__params = [], $tmp_helpers = null)
    {
        return $this->_template->render($__params, $tmp_helpers);
    }
}
