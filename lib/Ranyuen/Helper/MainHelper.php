<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Helper;

use Ranyuen\Model\Photo;

/**
 * Main view helper.
 */
class MainHelper extends Helper
{
    /**
     * Echo side navigation.
     *
     * @param Ranyuen\Navigation\Page[] $pages URIs and titles.
     *
     * @return string
     */
    public function echoSideNav($pages)
    {
        $output  = '<ul>';

        // if ($pages[0]->path === '/news/') {
        //     $first_ele = array_shift($pages);
        //     $pages = array_reverse($pages);
        //     array_unshift($pages, $first_ele);
        //     array_splice($pages, 10);
        // }

        foreach ((array)$pages as $page) {
            if (is_array($page)) {
                $output .= '<li>
    <a href="#">'.h($page[0]->title).'</a>
    '.$this->echoSideNav($page).'
</li>';
                continue;
            }
            $path = preg_replace('/\/\//', '/', $page->path);
            if ('ja' !== $page->lang) {
                $path = "/$page->lang$path";
            }
            $output .= '<li>
    <a href="'.h($path).'">'.h($page->title).'</a>
</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Echo breadcrumb navigation.
     *
     * @param Ranyuen\Navigation\Page[] $pages URIs and titles.
     *
     * @return string
     */
    public function echoBreadcrumb($pages)
    {
        $output = '';
        foreach ($pages as $page) {
            $output .= '<div class="nav-item">
    <a href="'.h(preg_replace('#//#', '/', $page->path)).'" itemprop="url">
        <span itemprop="title">'.h($page->title).'</span>
    </a>
</div>';
        }

        return $output;
    }

    /**
     * Echo lang navigation.
     *
     * @param array  $links       Top URIs.
     * @param strung $currentLang Current lang.
     *
     * @return string
     */
    public function echoSwitchLang($links, $currentLang)
    {
        $switchLang = [];
        foreach (['en' => 'English', 'ja' => '日本語'] as $k => $v) {
            $switchLang[] = $currentLang === $k ? $v : "<a href=\"{$links[$k]}\">$v</a>";
        }

        return implode(' / ', $switchLang);
    }

    /**
     * Echo a Youtube player.
     *
     * @param string $movieId YouTube movie ID.
     * @param string $title   Movie title.
     * @param int    $width   Widget width px.
     * @param int    $height  Wdget height px.
     *
     * @return string
     */
    public function echoYouTube($movieId, $title = '', $width = 800, $height = 450)
    {
        $output = "<div class=\"widget-youtube\">
<iframe width=\"$width\"
        height=\"$height\"
        src=\"//www.youtube.com/embed/$movieId?rel=0\"
        frameborder=\"0\"
        allowfullscreen></iframe>";
        if ($title) {
            $output .= "<div><a href=\"http://youtu.be/$movieId?rel=0\">".h($title).'</a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Echo an image.
     *
     * @param string $id     Photo UUID.
     * @param int    $width  Display width.
     * @param int    $height Display height.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function echoImg($id, $width = null, $height = null, $alt = null, $title = null, $class = null)
    {
        $photo = Photo::find($id);
        if (!$photo) {
            $photo = new Photo();
            $photo->id = $id;
            $photo->loadImageSize();
        }
        $src = $photo->getPath();
        if (!$width) {
            $width = $photo->width;
        }
        if (!$height) {
            $height = $photo->height;
        }
        $alt =  $alt . "";
        if (!$title) {
            $title = $alt;
        }
        if (empty($class)) {

            return '<img src="/'.h($src).'" alt="'.h($alt).'" title="'.h($title).'" width="'.h($width).'" height="'.h($height).'" />';
        } else {

            return '<img src="/'.h($src).'" alt="'.h($alt).'" title="'.h($title).'" width="'.h($width).'" height="'.h($height).'" class="'.h($class).'" />';
        }
    }
}
