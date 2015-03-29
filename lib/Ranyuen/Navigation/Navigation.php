<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 * Load config/nav.xml.
 */
class Navigation
{
    /**
     * Nav element.
     *
     * @var \SimpleXMLElement
     */
    private $nav;

    public function __construct()
    {
        $this->nav = simplexml_load_file('config/nav.xml');
    }

    /**
     * Side navigation.
     *
     * @param string $lang Current lang.
     * @param string $path URI path info.
     *
     * @return (Page|Page[])[]
     */
    public function getLocalNav($lang, $path)
    {
        $nav = $this->nav->xpath('/nav/lang[@name="'.h($lang).'"]')[0];
        $dirnames = $this->splitPath($path)[0];
        foreach ($dirnames as $dirname) {
            $nav = $nav->xpath('dir[@path="'.h($dirname).'"]')[0];
        }
        $parentPath = implode('/', array_slice($dirnames, 0, count($dirnames) - 1)).'/';
        if ('/' !== substr($parentPath, 0)) {
            $parentPath = '/'.$parentPath;
        }

        return (new DirElement($lang, $parentPath, $nav))->pages();
    }

    /**
     * パンくずリスト.
     *
     * @param string $lang Current lang.
     * @param string $path URI path info.
     *
     * @return Page[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getBreadcrumb($lang, $path)
    {
        $nav = $this->nav->xpath('/nav/lang[@name="'.h($lang).'"]')[0];
        $dirnames = $this->splitPath($path)[0];
        $parentPath = '/';
        $pages = [];
        $pages[] = (new DirElement($lang, $parentPath, $nav))->indexPage();
        foreach ($dirnames as $dirname) {
            $nav = $nav->xpath('dir[@path="'.h($dirname).'"]')[0];
            $pages[] = (new DirElement($lang, $parentPath, $nav))->indexPage();
            $parentPath .= $dirname.'/';
        }
        if (!preg_match('#/\z#', $path)) {
            $pages[] = Page::fromArticle($lang, Article::findByPath($path));
        }

        return $pages;
    }

    private function splitPath($path)
    {
        $pathparts = explode('/', ltrim($path, '/'));
        $dirnames = array_slice($pathparts, 0, count($pathparts) - 1);
        $filename = $pathparts[count($pathparts) - 1];

        return [$dirnames, $filename];
    }
}
