<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

/**
 * API
 */
abstract class _ApiController extends Controller
{
    /**
     * @Inject
     * @var Ranyuen\Logger
     */
    private $logger;

    /**
     * @param string $method HTTP method
     * @param array  $params Params.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render($method, array $params)
    {
        switch ($method) {
            case 'GET':
                $response = $this->get($params);
                break;
            case 'POST':
                $response = $this->post($params);
                break;
            case 'PUT':
                $response = $this->put($params);
                break;
            case 'DELETE':
                $response = $this->delete($params);
                break;
            case 'OPTIONS':
                $response = $this->options($params);
                break;
            case 'PATCH':
                $response = $this->patch($params);
                break;
        }
        if ($response) {
            echo is_array($response) ? json_encode($response) : $response;
        }
        if ($this->logger) {
            $this->logger->addAccessInfo();
        }
    }
}
