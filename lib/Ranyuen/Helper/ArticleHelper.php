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
        return implode('', array_map(
            function ($article) {
                $lang  = $this->html($article->lang);
                $url   = $this->html($article->url);
                $title = $this->html($article->title);

                return "<div>
    <a href=\"/$lang/news/$url\">$title</a>
</div>";
            },
            $articles
        ));
    }
}
