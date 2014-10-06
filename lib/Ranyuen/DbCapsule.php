<?php
namespace Ranyuen;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

class DbCapsule
{
    /** @var Manager */
    private $_capsule;

    /**
     * @param array $config
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->_capsule = new Manager();
        $this->_capsule->addConnection($config);
        $dispatcher = new Dispatcher(new Container());
        $dispatcher->listen('illuminate.query',
            function ($query, $bindings, $time, $name) use ($logger) {
                $message = [
                    'log.type'   => 'db',
                    'query'      => $query,
                    'time'       => $time,
                    'connection' => $name,
                ];
                foreach ($bindings as $k => $v) {
                    $message["bindings.$k"] = strval($v);
                }
                $logger->addInfo($message);
            });
        $this->_capsule->setEventDispatcher($dispatcher);
        $this->_capsule->setAsGlobal();
        $this->_capsule->bootEloquent();
        $this->getConnection()->enableQueryLog();
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->_capsule->connection();
    }

    /**
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        return $this->getConnection()->getSchemaBuilder();
    }
}
