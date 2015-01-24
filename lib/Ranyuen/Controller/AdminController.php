<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;
use Ranyuen\Model\Admin;
use Ranyuen\Model\Article;
use Ranyuen\Model\ArticleTag;
use Ranyuen\Renderer;

/**
 * Admin
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @Route('/admin')
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

    /** @Route('/login') */
    public function showLogin($username = null, $password = null)
    {
        return $this->renderer->render(
            'admin/login',
            [
                'username'       => $username,
                'password'       => $password,
                'admin_username' => $this->session['admin_username'],
            ]
        );
    }

    /** @Route('/login',via=POST) */
    public function login($username, $password)
    {
        if (!Admin::isAuth($username, $password)) {
            return new Response($this->showLogin($username, $password), 401);
        }
        $this->session['admin_username'] = $username;

        return new Response('', 303, ['Location' => '/admin/']);
    }

    /** @Route('/logout') */
    public function logout()
    {
        unset($this->session['admin_username']);

        return new Response('', 303, ['Location' => '/admin/login']);
    }

    /** @Route('/') */
    public function index()
    {
        $this->auth();

        return $this->renderer->render(
            'admin/index',
            [
                'admin_username' => $this->session['admin_username'],
                'articles'       => Article::all(),
                'article_tags'   => ArticleTag::all(),
            ]
        );
    }

    protected function auth()
    {
        if (!isset($this->session['admin_username'])) {
            throw new Http403ForbiddenException('/admin/login');
        }
    }
}
