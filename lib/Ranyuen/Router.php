<?php
namespace Ranyuen;

use \Slim;

class Router extends Slim\Slim
{
    private $_config;

    public function __construct($config)
    {
        parent::__construct();
        $this->_config = $config;
        $this->config($config);
        $this->setDefaultRouteConditions();
    }

    private function setDefaultRouteConditions()
    {
        $langs = (new Navigation($this->_config))->getLangs();
        $lang_regex = implode('|', $langs);
        Slim\Route::setDefaultConditions([
            'lang' => $lang_regex
        ]);
    }
}
