<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use Ranyuen\Model\Admin;
use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleTag;
use Ranyuen\Renderer;

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
    /** @var Ranyuen\Renderer */
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

    public function __construct(Renderer $renderer)
    {
        $renderer->setLayout('admin/layout');
        $this->renderer = $renderer;
    }

    public function showLogin()
    {
        echo $this->renderer->render(
            'admin/login',
            [
                'admin_username' => $this->session['admin_username'],
            ]
        );
        $this->logger->addAccessInfo();
    }

    public function login($username, $password)
    {
        if (!Admin::isAuth($username, $password)) {
            echo $this->renderer->render(
                'admin/login',
                [
                    'username'       => $username,
                    'password'       => $password,
                    'admin_username' => $this->session['admin_username'],
                ]
            );
            $this->logger->addAccessInfo();

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
        if ($this->auth()) {
            echo $this->renderer->render(
                'admin/index',
                [
                    'admin_username' => $this->session['admin_username'],
                    'articles'       => Article::all(),
                    'article_tags'   => ArticleTag::all(),
                ]
            );
        }
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
