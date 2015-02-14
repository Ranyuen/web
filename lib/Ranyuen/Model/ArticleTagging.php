<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * Article tagging model.
 */
class ArticleTagging extends Eloquent\Model
{
    public $timestamps = false;

    protected $table = 'article_tagging';
    protected $attributes = [
        'is_primary' => false,
    ];
    protected $fillable = ['article_id', 'article_tag_id', 'is_primary'];

    public function article()
    {
        return $this->belongsTo('Ranyuen\Model\Article');
    }

    public function tag()
    {
        return $this->belongsTo('Ranyuen\Model\ArticleTag');
    }
}
