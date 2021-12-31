<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
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
     * HTTP request Router.
     *
     * @var Ranyuen\Router
     *
     * @Inject
     */
    protected $router;
    /**
     * Renderer.
     *
     * @var Ranyuen\Template\ViewRenderer
     */
    protected $renderer;
    /**
     * Logger.
     *
     * @var Ranyuen\Logger
     *
     * @Inject
     */
    protected $logger;
    /**
     * Session storage.
     *
     * @var Ranyuen\Session
     *
     * @Inject
     */
    protected $session;

    public function __construct(ViewRenderer $renderer)
    {
        $renderer->setLayout('admin/layout');
        $this->renderer = $renderer;
    }

    /**
     * Show login page.
     *
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
     * Process login.
     *
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
     * Process logout.
     *
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
     * Admin top page.
     *
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

    /**
     * Admin php info.
     *
     * @return string
     *
     * @Route('/checkInfo')
     */
    public function checkInfo()
    {
        $this->auth();

        return $this->renderer->render(
            'admin/index',
            [
                'admin_username' => $this->session['admin_username'],
                'info' => phpinfo()
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
