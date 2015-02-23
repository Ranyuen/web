<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

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
