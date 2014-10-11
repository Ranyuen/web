<?php
namespace Ranyuen\Helper;

/**
 * Helper methods using in the view.
 */
class Helper
{
    /**
     * @param  array  $nav
     * @param  string $base
     * @return string
     */
    public function echoNav($nav, $base = '/')
    {
        $output = '';
        $is_first = true;
        foreach ($nav as $href => $title) {
            if (!$title) {
                continue;
            }
            $output .= '<div class="nav-item ' .
                ($is_first ? 'nav-item-home' : '') .
                '"><a href="' .
                $this->h(preg_replace('/\/\//', '/', $base . $href)) .
                '">' .
                $this->h($title) .
                '</a></div>';
            $is_first = false;
        }

        return $output;
    }

    /**
     * @param  array  $nav
     * @param  string $base
     * @return string
     */
    public function echoBreadcrumb($nav, $base = '/')
    {
        $output = '<div class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
        foreach ($nav as $href => $title) {
            if (!$title) {
                continue;
            }
            $output .= '<div class="nav-item"><a href="' .
                $this->h(preg_replace('/\/\//', '/', $base . $href)) .
                '" itemprop="url"><span itemprop="title">' .
                $this->h($title) .
                '</span></a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * @param  array  $links
     * @param  strung $current_lang
     * @return string
     */
    public function echoSwitchLang($links, $current_lang)
    {
        $switch_lang = [];
        foreach (['en' => 'English', 'ja' => '日本語'] as $k => $v) {
          $switch_lang[] =  $current_lang === $k ? $v : "<a href=\"{$links[$k]}\">$v</a>";
        }

        return implode(' / ', $switch_lang);
    }

    /**
     * @param  string  $movie_id
     * @param  string  $title
     * @param  integer $width
     * @param  integer $height
     * @return string
     */
    public function echoYouTube($movie_id, $title = '', $width = 560, $height = 315)
    {
        $output = "<div class=\"widget-youtube\"><iframe width=\"$width\" height=\"$height\" src=\"//www.youtube.com/embed/$movie_id\" frameborder=\"0\" allowfullscreen></iframe>";
        if ($title) {
            $output .= "<div><a href=\"http://youtu.be/$movie_id\">" .
                $this->h($title) .
                '</a></div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Escape all HTML5 special charactors.
     *
     * @param  string $str
     * @return string
     */
    private function h($str)
    {
        return htmlspecialchars(
            $str,
            ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5,
            'utf-8'
        );
    }
}
