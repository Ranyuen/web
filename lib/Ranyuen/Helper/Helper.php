<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Helper;

/**
 * Main view helper.
 */
class Helper
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
    public function echoYouTube($movieId, $title = '', $width = 560, $height = 315)
    {
        $output = "<div class=\"widget-youtube\">
<iframe width=\"$width\"
        height=\"$height\"
        src=\"//www.youtube.com/embed/$movieId\"
        frameborder=\"0\"
        allowfullscreen></iframe>";
        if ($title) {
            $output .= "<div><a href=\"http://youtu.be/$movieId\">".
                $this->html($title).
                '</a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Escape all HTML5 special charactors.
     *
     * @param string $str Raw string
     *
     * @return string
     */
    private function html($str)
    {
        if (is_array($str)) {
            $str = implode(', ', $str);
        } elseif (!is_string($str)) {
            $str = strval($str);
        }

        return htmlspecialchars(
            $str,
            ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5,
            'utf-8'
        );
    }
}
