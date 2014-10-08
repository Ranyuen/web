<?php
namespace Ranyuen\Controller;

use Ranyuen\App;

abstract class Controller
{
    /** @var App */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }
}
