<?php
namespace Ranyuen\Api;

interface ApiController
{
    public function render($method, array $uri_params, array $request_params);
}
