<?php
namespace Ranyuen\Controller;

use \Ranyuen\App;
use \ReflectionClass;

class ApiController extends Controller
{
    /** @var \Ranyuen\Logger */
    private $_logger;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->_logger = $app->getContainer()['logger'];
    }

    /**
     * @param string   $api_name
     * @param string   $method
     * @param string[] $uri_params
     * @param array    $request_params
     */
    public function renderApi($api_name, $method, array $uri_params, array $request_params)
    {
        $api_name = preg_replace_callback('/[-_](.)/', function ($m) {
            return strtoupper($m[1]);
        }, ucwords(strtolower($api_name)));
        $controller =
            (new ReflectionClass("\Ranyuen\\Controller\\Api$api_name"))
            ->newInstance();
        $params = array_merge($uri_params, $request_params);
        switch ($method) {
        case 'GET':
            $response = $controller->get($params);
            break;
        case 'POST':
            $response = $controller->post($params);
            break;
        case 'PUT':
            $response = $controller->put($params);
            break;
        case 'DELETE':
            $response = $controller->delete($params);
            break;
        case 'OPTIONS':
            $response = $controller->options($params);
            break;
        case 'PATCH':
            $response = $controller->patch($params);
            break;
        }
        if (!$response) {
            return;
        }
        echo is_array($response) ? json_encode($response) : $response;
        $this->_logger->addAccessInfo();
    }
}
