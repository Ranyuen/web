<?php
namespace Ranyuen;

use Monolog;
use Monolog\Handler\RotatingFileHandler;
use Clover\Text\LTSV;

class Logger
{
    private $config;
    private $log;

    /**
     * @param string $name
     * @param array  $config
     */
    public function __construct($name, $config)
    {
        $this->config = $config;
        $this->log = new Monolog\Logger($name);
        $this->log->pushHandler(new RotatingFileHandler(
            "{$this->config['log.path']}/$name.log",
            0,
            $this->config['log.level']));
    }

    /**
     * @return Logger
     */
    public function addAccessInfo()
    {
        $ltsv = new LTSV();
        $ltsv->add('time', $_SERVER['REQUEST_TIME_FLOAT']);
        $ltsv->add('host', $_SERVER['REMOTE_ADDR']);
        $ltsv->add('method', $_SERVER['REQUEST_METHOD']);
        $ltsv->add('uri', $_SERVER['REQUEST_URI']);
        $ltsv->add('protocol', $_SERVER['SERVER_PROTOCOL']);
        $ltsv->add('status', http_response_code());
        if (isset($_SERVER['HTTP_REFERER']))
            $ltsv->add('referer', $_SERVER['HTTP_REFERER']);
        if (isset($_SERVER['HTTP_USER_AGENT']))
            $ltsv->add('ua', $_SERVER['HTTP_USER_AGENT']);
        $this->log->addInfo($ltsv->toLine());

        return $this;
    }
}
