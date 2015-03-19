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
    /** @Inject */
    private $config;
    private $nav;

    public function __construct()
    {
        $this->nav = simplexml_load_file('config/nav.xml');
    }

    /**
     * @param string $lang Current lang.
     * @param string $path Current template name.
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
        $pages = (new DirElement($lang, implode('/', $dirs).'/', $nav))->pages();
        //var_dump($pages);
        return $pages;
    }

    /**
     * @param string $lang         Current lang
     * @param string $templateName Current template name
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

    private function expandPages($nav)
    {
        $pages = [];
        foreach ($nav->children() as $elm) {
            switch ($elm->getName()) {
            case 'dir':
                break;
            case 'page':
                break;
            case 'rest-pages':
                break;
            case 'latest-pages':
                break;
            }
        }
        return $pages;
    }

    // private function gather($nav)
    // {
    //     $index = [];
    //     $local = [];
    //     foreach ($nav->children() as $elm) {
    //         if ($elm->getName() === 'page') {
    //             if ((string) $elm['path'] === 'index') {
    //                 $index['/'] = (string) $elm['title'];
    //             } else {
    //                 $local[(string) $elm['path']] = (string) $elm['title'];
    //             }
    //         } else {
    //             if ($elm->xpath("page[@path='index']")) {
    //                 $local[(string) $elm['path'].'/'] = (string) $elm->xpath("page[@path='index']")[0]['title'];
    //             }
    //         }
    //     }
    //     $local = array_merge($index, $local);

    //     return $local;
    // }
}
