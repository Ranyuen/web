<?php
namespace Ranyuen;

use Clover\Text\LTSV;
use Monolog;
use Monolog\Handler\RotatingFileHandler;

class Logger extends Monolog\Logger
{
    /**
     * @param string $name
     * @param array  $config
     */
    public function __construct($name, $config)
    {
        parent::__construct($name);
        $this->pushHandler(new RotatingFileHandler(
            "{$config['log.path']}/$name.log",
            0,
            $config['log.level'])
        );
    }

    /**
     * Override.
     *
     * @param  integer      $level   The logging level
     * @param  string|array $message The log message
     * @param  array        $context The log context
     * @return Boolean      Whether the record has been processed
     */
    public function addRecord($level, $message, array $context = [])
    {
        if (is_array($message)) {
            $ltsv = new LTSV();
            foreach ($message as $k => $v) {
                $ltsv->add($k, $v);
            }
            $message = $ltsv->toLine();
        }

        return parent::addRecord($level, $message, $context);
    }

    /**
     * @return Boolean
     */
    public function addAccessInfo()
    {
        $message = [
            'log.type' => 'http',
            'time'     => $_SERVER['REQUEST_TIME_FLOAT'],
            'host'     => $_SERVER['REMOTE_ADDR'],
            'method'   => $_SERVER['REQUEST_METHOD'],
            'uri'      => $_SERVER['REQUEST_URI'],
            'protocol' => $_SERVER['SERVER_PROTOCOL'],
            'status'   => http_response_code(),
        ];
        if (isset($_SERVER['HTTP_REFERER'])) {
            $message['referer'] = $_SERVER['HTTP_REFERER'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $message['ua'] = $_SERVER['HTTP_USER_AGENT'];
        }

        return $this->addInfo($message);
    }
}
