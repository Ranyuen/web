<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

use Illuminate\Database\Capsule\Manager;

// use Illuminate\Events\Dispatcher;

/**
 * DB connection.
 */
class DbCapsule
{
    /**
     * Nakid DB capsule.
     *
     * @var Manager
     */
    private $capsule;

    /**
     * Constructor.
     *
     * @param Logger $logger Logger.
     * @param array  $config Application config.
     */
    public function __construct(Logger $logger, array $config)
    {
        $this->capsule = new Manager();
        $this->capsule->addConnection($config['db']);
        $logger;
        // $dispatcher = new Dispatcher(new Illuminate\Container\Container());
        // $dispatcher->listen(
        //     'illuminate.query',
        //     function ($query, $bindings, $time, $name) use ($logger) {
        //         $message = [
        //             'log.type'   => 'db',
        //             'query'      => $query,
        //             'time'       => $time,
        //             'connection' => $name,
        //         ];
        //         foreach ($bindings as $k => $v) {
        //             $message["bindings.$k"] = strval($v);
        //         }
        //         $logger->addInfo($message);
        //     }
        // );
        // $this->capsule->setEventDispatcher($dispatcher);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
        $this->getConnection()->enableQueryLog();
    }

    /**
     * Get a DB connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->capsule->connection();
    }

    /**
     * Do a transaction.
     *
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
     * Get a table schema builder.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        return $this->getConnection()->getSchemaBuilder();
    }
}
