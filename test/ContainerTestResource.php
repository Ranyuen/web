<?php
namespace ContainerTestResource;

class Config { }

class Momonga { }

class InjectToConstructor
{
    /** @var string */
    public $arg1;
    /** @var Config */
    public $cfg;
    /** @var integer */
    public $num;
    /** @var string */
    public $arg2;
    /** @var Momonga */
    public $momonga;

    /**
     * @Inject
     * @param string  $arg1
     * @param Config  $cfg
     * @param integer $num
     * @param string  $arg2
     * @param Momonga $momonga
     */
    public function __construct($arg1, Config $cfg, $num, $arg2, Momonga $momonga)
    {
        $this->arg1    = $arg1;
        $this->cfg     = $cfg;
        $this->num     = $num;
        $this->arg2    = $arg2;
        $this->momonga = $momonga;
    }
}

class InjectToProperties
{
    /** @var string */
    public $arg1;
    /**
     * @Inject
     * @var ContainerTestResource\Config
     */
    public $cfg;
    /**
     * @Inject
     * @var integer
     */
    public $num;
    /** @var string */
    public $arg2;
    /**
     * @Inject
     * @var ContainerTestResource\Momonga
     */
    public $momonga;

    /**
     * @param string $arg1
     * @param string $arg2
     */
    public function __construct($arg1, $arg2)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}
