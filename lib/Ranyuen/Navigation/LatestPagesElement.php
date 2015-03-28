<?php
/**
 * Ranyuen web site.
 */
namespace Ranyuen\Navigation;

use Ranyuen\Model\Article;

/**
 */
class LatestPagesElement
{
    private $dir;
    private $elm;
    private $count;

    public function __construct(DirElement $dir, \SimpleXMLElement $elm)
    {
        $this->dir   = $dir;
        $this->elm   = $elm;
        $this->count = intval($elm->attributes()['count']);
    }

    public function pages()
    {
        $existingPaths = array_map(
            function ($page) {
                return $page->path;
            },
            $this->dir->childPages()
        );
        $articles = Article::children($this->dir->path, $this->count);
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
