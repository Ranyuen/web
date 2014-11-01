<?php
/**
 * API
 */
namespace Ranyuen\Controller;

use ReflectionClass;

/**
 * API
 */
class ApiController extends Controller
{
    /**
     * @Inject
     * @var \Ranyuen\Logger
     */
    private $logger;

    /**
     * @param string   $apiName       API name
     * @param string   $method        HTTP method
     * @param string[] $uriParams     Params in the URI
     * @param array    $requestParams Params in the HTTP param
     *
     * @return void
     */
    public function renderApi($apiName, $method, array $uriParams, array $requestParams)
    {
        $apiName = preg_replace_callback(
            '/[-_](.)/',
            function ($m) {
                return strtoupper($m[1]);
            },
            ucwords(strtolower($apiName))
        );
        $controller = (new ReflectionClass("\Ranyuen\\Controller\\Api$apiName"))->newInstance();
        $params = array_merge($uriParams, $requestParams);
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
        $this->logger->addAccessInfo();
    }
}
