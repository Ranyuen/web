<?php

/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Response;
use Ranyuen\Model\Admin;
use Ranyuen\Model\Article;
use Ranyuen\Template\ViewRenderer;

/**
 * Admin.
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
    /** @var Ranyuen\Template\ViewRenderer */
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

    public function __construct(ViewRenderer $renderer)
    {
        $renderer->setLayout('admin/layout');
        $this->renderer = $renderer;
    }

    /**
     * @param string $username User name.
     * @param string $password Raw password.
     *
     * @return string
     *
     * @Route('/login')
     */
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

    /**
     * @param string $username User name.
     * @param string $password Raw password.
     *
     * @return string
     *
     * @Route('/login',via=POST)
     */
    public function login($username, $password)
    {
        if (!Admin::isAuth($username, $password)) {
            return new Response($this->showLogin($username, $password), 403);
        }
        $this->session['admin_username'] = $username;

        return new Response('', 303, ['Location' => '/admin/']);
    }

    /**
     * @return string
     *
     * @Route('/logout')
     */
    public function logout()
    {
        unset($this->session['admin_username']);

        return new Response('', 303, ['Location' => '/admin/login']);
    }

    /**
     * @return string
     *
     * @Route('/')
     */
    public function index()
    {
        $this->auth();

        return $this->renderer->render(
            'admin/index',
            [
                'admin_username' => $this->session['admin_username'],
                'articles'       => Article::with('contents')->get(),
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
