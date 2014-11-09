<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\Admin;

/**
 * Admin
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AdminController extends Controller
{
    /**
     * @var Ranyuen\Router
     * @Inject
     */
    protected $router;
    /**
     * @var Ranyuen\Renderer
     * @Inject
     */
    protected $renderer;
    /**
     * @var Ranyuen\Logger
     * @Inject
     */
    protected $logger;
    /**
     * @var Ranyuen\Session
     * @Inject
     */
    protected $session;

    public function showLogin()
    {
        $this->renderer->setLayout('admin/layout');
        echo $this->renderer->render(
            'admin/login',
            [
                'admin_username' => $this->session['admin_username'],
            ]
        );
        $this->logger->addAccessInfo();
    }

    public function login($username, $rawPassword)
    {
        $this->renderer->setLayout('admin/layout');
        if (!Admin::isAuth($username, $rawPassword)) {
            echo $this->renderer->render(
                'admin/login',
                [
                    'username'       => $username,
                    'password'       => $rawPassword,
                    'admin_username' => $this->session['admin_username'],
                ]
            );

            return;
        }
        $this->session['admin_username'] = $username;
        $this->router->response->redirect('/admin/', 303);
        $this->logger->addAccessInfo();
    }

    public function logout()
    {
        unset($this->session['admin_username']);
        $this->router->response->redirect('/admin/login', 303);
        $this->logger->addAccessInfo();
    }

    public function index()
    {
        $this->auth();
        $this->renderer->setLayout('admin/layout');
        echo $this->renderer->render(
            'admin/index',
            [
                'admin_username' => $this->session['admin_username'],
            ]
        );
        $this->logger->addAccessInfo();
    }

    protected function auth()
    {
        if (!isset($this->session['admin_username'])) {
            $this->router->halt(403, '403 Forbidden <a href="/admin/login">Login</a>');

            return false;
        }

        return true;
    }
}
