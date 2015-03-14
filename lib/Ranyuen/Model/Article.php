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
}
