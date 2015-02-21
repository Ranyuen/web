<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Model;

use dflydev\markdown\MarkdownExtraParser;
use Illuminate\Database\Eloquent;
use Ranyuen\Template;

/**
 * Article model.
 */
class Article extends Eloquent\Model
{
    protected $table = 'article';
    protected $fillable = ['path'];

    public function contents()
    {
        return $this->hasMany('Ranyuen\Model\ArticleContent');
    }
}
