<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * Article tag model.
 */
class ArticleTag extends Eloquent\Model
{
    public static function allPrimaryTag()
    {
        return self::with('articles')->whereHas(
            'taggings',
            function ($q) {
                $q->where('is_primary', true);
            }
        )->get();
    }

    /**
     * @param string $name name_ja or name_en.
     *
     * @return mixed
     */
    public static function findByName($name)
    {
        return ArticleTag::whereRaw(
            'name_ja = ? OR name_en = ?',
            [$name, $name]
        )->first();
    }

    protected $table = 'article_tag';
    protected $fillable = ['name_ja', 'name_en'];

    public function articles()
    {
        return $this->belongsToMany('Ranyuen\Model\Article', 'article_tagging');
    }

    public function taggings()
    {
        return $this->hasMany('Ranyuen\Model\ArticleTagging');
    }
}
