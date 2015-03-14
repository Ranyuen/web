<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen;

use Illuminate;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

/**
 * DB connection.
 */
class DbCapsule
{
    /** @var Manager */
    private $capsule;

    /**
     * @param Logger $logger Logger
     * @param array  $config Application config
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->capsule = new Manager();
        $this->capsule->addConnection($config['db']);
        $dispatcher = new Dispatcher(new Illuminate\Container\Container());
        $dispatcher->listen(
            'illuminate.query',
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
            }
        );
        $this->capsule->setEventDispatcher($dispatcher);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
        $this->getConnection()->enableQueryLog();
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->capsule->connection();
    }

    /**
     * @param \Closure $callback Transaction process.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function transaction(\Closure $callback)
    {
        return $this->getConnection()->transaction($callback);
    }

    /**
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        return $this->getConnection()->getSchemaBuilder();
    }
}
