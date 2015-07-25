<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * Article model.
 */
class Article extends Eloquent\Model
{
    public static function findByPath($path)
    {
        return self::with('contents')->where(['path' => $path])->first();
    }

    public static function children($path, $count = 0)
    {
        $entities = self::with('contents')
            ->where('path', 'LIKE', str_replace('%', '\\%', $path).'%')
            ->orderBy('id', 'ASC')
            ->get();
        $articles = [];
        foreach ($entities as $entity) {
            if (!preg_match('#^'.preg_quote($path, '#').'[^/]*$#', $entity->path)) {
                continue;
            }
            $articles[] = $entity;
            if ($count && count($articles) >= $count) {
                break;
            }
        }

        return $articles;
    }

    protected $table    = 'article';
    protected $fillable = ['path'];

    public $wild = array(
         64,  65,
         66,  67,  68,  69,  70,
         71,  72,  73,  74,  75,
         76,  77,  78,  79,  80,
         81,  82,  83,  84,  85,
         86,  87,  88,  89,  90,
         91,  92,  93,  94,  95,
         96,  97,  98,  99, 100,
        101, 102, 103, 104, 105,
        106, 107, 108, 109,
        111,      113, 114, 115,
        116, 117, 118, 119, 120,
        121, 122, 123, 124, 125,
        126, 128
    );

    public $dream = array(
        127
    );

    public function contents()
    {
        return $this->hasMany('Ranyuen\Model\ArticleContent');
    }

    public function getContent($lang)
    {
        return ArticleContent::where(['article_id' => $this->id, 'lang' => $lang])->first();
    }

    /**
     * Syncronize this with the article.
     *
     * @param Article $article Source.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function sync(Article $article)
    {
        \DB::transaction(
            function () use ($article) {
                $this->path = $article->path;
                $this->save();
                for ($i = 0; isset($article->contents[$i]) && isset($this->contents[$i]); ++$i) {
                    $this->contents[$i]->lang    = $article->contents[$i]->lang;
                    $this->contents[$i]->content = $article->contents[$i]->content;
                }
                if (count($article->contents) < count($this->contents)) {
                    foreach (array_slice($this->contents, count($article->contents)) as $content) {
                        $content->delete();
                    }
                } elseif (count($article->contents) > count($this->contents)) {
                    foreach (array_slice($article->contents, count($this->contents)) as $content) {
                        $newContent = new ArticleContent(
                            [
                            'lang'    => $content->lang,
                            'content' => $content->content,
                            ]
                        );
                        $newContent->article_id = $this->id;
                        $this->contents[] = $newContent;
                    }
                }
                $this->push();
            }
        );
    }
}
