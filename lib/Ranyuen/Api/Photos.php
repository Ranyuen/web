<?php
namespace Ranyuen\Api;

use \Ranyuen\Model\Photo;

class Photos implements ApiController
{
    public function render($method, array $uri_params, array $request_params)
    {
        return ['ok' => 'Photos'];
    }
}
