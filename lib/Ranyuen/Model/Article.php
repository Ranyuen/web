<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Model;

use dflydev\markdown\MarkdownExtraParser;
use Illuminate\Database\Eloquent;

/**
 * Article model.
 */
class Article extends Eloquent\Model
{
    protected $table = 'article';
    protected $fillable = ['title', 'description', 'content', 'url', 'lang'];

    public function tags()
    {
        return $this->belongsToMany('Ranyuen\Model\ArticleTag', 'article_tagging');
    }

    public function taggings()
    {
        return $this->hasMany('Ranyuen\Model\ArticleTagging');
    }

    /**
     * @return string
     */
    public function plainTitle()
    {
        return strip_tags((new MarkdownExtraParser())->transformMarkdown($this->title));
    }

    /**
     * @param string[] $tagNames Sync ArticleTags. [primary, sub, ...]
     *
     * @return boolean
     */
    public function syncTagsByTagNames($tagNames)
    {
        $tags = array_map(
            function ($tag) {
                $tag = trim($tag);

                return ArticleTag::findByname($tag)->id;
            },
            $tagNames
        );
        $hasSaved = true;
        foreach ($this->taggings as $tagging) {
            $tagging->is_primary = false;
            $hasSaved = $tagging->save() && $hasSaved;
        }
        $hasSaved = ArticleTagging::firstOrCreate(['article_id' => $this->id, 'article_tag_id' => $tags[0]])
            ->update(['is_primary' => true])
            && $hasSaved;
        $hasSaved = $this->tags()->sync($tags) && $hasSaved;

        return $hasSaved;
    }
}
