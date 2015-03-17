<?php
/**
 * Ranyuen web site.
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
        return Article::where(['path' => $path])->first();
    }

    protected $table = 'article';
    protected $fillable = ['path'];

    public function contents()
    {
        return $this->hasMany('Ranyuen\Model\ArticleContent');
    }

    public function getContent($lang)
    {
        return ArticleContent::where(['article_id' => $this->id, 'lang' => $lang])->first();
    }

    public function sync(Article $article)
    {
        \DB::transaction(function () use ($article) {
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
                    $newContent = new ArticleContent([
                        'lang'    => $content->lang,
                        'content' => $content->content,
                    ]);
                    $newContent->article_id = $this->id;
                    $this->contents[] = $newContent;
                }
            }
            $this->push();
        });
    }
}
