<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 */
class RestPagesElement
{
    private $dir;
    private $elm;

    function __construct(DirElement $dir, \SimpleXMLElement $elm)
    {
        $this->dir = $dir;
        $this->elm = $elm;
    }

    public function pages()
    {
        $existingPaths = array_map(
            function ($page) {
                return $page->path;
            },
            $this->dir->childPages()
        );
        $articles = Article::children($this->dir->path);
        $pages = [];
        foreach ($articles as $article) {
            if (in_array($article->path, $existingPaths)
                || !$article->getContent($this->dir->lang)) {
                continue;
            }
            $pages[] = Page::fromArticle($this->dir->lang, $article);
        }
        return $pages;
    }
}
