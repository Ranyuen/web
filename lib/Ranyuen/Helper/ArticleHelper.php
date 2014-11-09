<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Helper;

/**
 * Article view helper.
 */
class ArticleHelper extends Helper
{
    public function echoNews($articles)
    {
        $result = '';
        foreach ($articles as $article) {
            $lang  = $this->html($article->lang);
            $url   = $this->html($article->url);
            $title = $this->html($article->title);
            $result .= "<div>
    <a href=\"/$lang/news/$url\">$title</a>
</div>";
        }

        return $result;
    }
}
