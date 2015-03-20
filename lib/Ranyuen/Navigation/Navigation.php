<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Navigation;

/**
 * Load navigation config and manipulate.
 */
class Navigation
{
    /**
     * @var \SimpleXMLElement
     */
    private $nav;

    public function __construct()
    {
        $this->nav = simplexml_load_file('config/nav.xml');
    }

    /**
     * @param string $lang Current lang.
     * @param string $path URI path info.
     *
     * @return array
     */
    public function getLocalNav($lang, $path)
    {
        $nav = $this->nav->xpath('/nav/lang[@name="'.h($lang).'"]')[0];
        $path = explode('/', ltrim($path, '/'));
        $dirs = array_slice($path, 0, count($path) - 1);
        $file = $path[count($path) - 1];
        foreach ($dirs as $dir) {
            $nav = $nav->xpath('dir[@path="'.h($dir).'"]')[0];
        }
        $parentPath = implode('/', array_slice($dirs, 0, count($dirs) - 1)).'/';
        return (new DirElement($lang, $parentPath, $nav))->pages();
    }

    /**
     * @param string $lang         Current lang.
     * @param string $templateName URI path info.
     *
     * @return array
     */
    public function getBreadcrumb($lang, $templateName)
    {
        $nav = $this->nav->xpath('/nav/lang[@name="'.h($lang).'"]')[0];
        $path = '/';
        $breadcrumb = [$path => (string) $nav->page[0]['title']];
        foreach (explode('/', $templateName) as $part) {
            $part = h($part);
            if ('index' === $part) {
                break;
            }
            if ($nav->xpath("*[@path='$part']")) {
                $nav = $nav->xpath("*[@path='$part']")[0];
                $path .= $part;
                if ($nav->getName() === 'dir') {
                    $path .= '/';
                }
            } else {
                break;
            }
            if ($nav->xpath("*[@path='index']")) {
                $breadcrumb[$path] = (string) $nav->xpath("*[@path='index']")[0]['title'];
            } elseif ($nav->getName() === 'page') {
                $breadcrumb[$path] = (string) $nav['title'];
            }
        }

        return $breadcrumb;
    }
}
