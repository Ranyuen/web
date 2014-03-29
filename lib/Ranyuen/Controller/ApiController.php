<?php
namespace Ranyuen\Controller;

interface ApiController
{
    public function render($method, array $uri_params, array $request_params);
}
