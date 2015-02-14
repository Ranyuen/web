<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Helper;

use Ranyuen\Model\Photo;

/**
 * Main view helper.
 */
class MainHelper extends Helper
{
    /**
     * @param array  $nav  URIs and titles
     * @param string $base Base URI
     *
     * @return string
     */
    public function echoNav($nav, $base = '/')
    {
        $output = '';
        $isFirst = true;
        foreach ($nav as $href => $title) {
            if (!$title) {
                continue;
            }
            $output .= '<div class="nav-item '.
                ($isFirst ? 'nav-item-home' : '').
                '"><a href="'.
                $this->html(preg_replace('/\/\//', '/', $base.$href)).
                '">'.
                $this->html($title).
                '</a></div>';
            $isFirst = false;
        }

        return $output;
    }

    /**
     * @param array  $nav  URIs and titles
     * @param string $base Base URI
     *
     * @return string
     */
    public function echoBreadcrumb($nav, $base = '/')
    {
        $output = '<div class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
        foreach ($nav as $href => $title) {
            if (!$title) {
                continue;
            }
            $output .= '<div class="nav-item"><a href="'.
                $this->html(preg_replace('/\/\//', '/', $base.$href)).
                '" itemprop="url"><span itemprop="title">'.
                $this->html($title).
                '</span></a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * @param array  $links       Top URIs
     * @param strung $currentLang Current lang
     *
     * @return string
     */
    public function echoSwitchLang($links, $currentLang)
    {
        $switchLang = [];
        foreach (['en' => 'English', 'ja' => '日本語'] as $k => $v) {
            $switchLang[] =  $currentLang === $k ? $v : "<a href=\"{$links[$k]}\">$v</a>";
        }

        return implode(' / ', $switchLang);
    }

    /**
     * @param string  $movieId YouTube movie ID
     * @param string  $title   Movie title
     * @param integer $width   Widget width px
     * @param integer $height  Wdget height px
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
            $output .= "<div><a href=\"http://youtu.be/$movieId?rel=0\">".
                $this->html($title).
                '</a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * @param string  $id     Photo UUID.
     * @param integer $width  Display width.
     * @param integer $height Display height.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function echoImg($id, $width = null, $height = null)
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
        $alt = $this->html("$photo->description_ja $photo->description_en 蘭裕園(Ranyuen)");
        $width = $this->html($width);
        $height = $this->html($height);

        return "<img src=\"$src\"
    alt=\"$alt\"
    width=\"$width\"
    height=\"$height\"/>";
    }
}
