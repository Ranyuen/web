<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Model;

use dflydev\markdown\MarkdownExtraParser;
use Illuminate\Database\Eloquent;
use Ranyuen\Template\Template;

class ArticleContent extends Eloquent\Model
{
    protected $table = 'article_content';
    protected $fillable = ['lang', 'content'];

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        $params = (new Template($this->content))->params;
        if (isset($params[$name])) {
            return $params[$name];
        }

        return parent::__get($name);
    }

    public function article()
    {
        return $this->belongsTo('Ranyuen\Model\Article');
    }

    /**
     * @return string
     */
    public function plainTitle()
    {
        return strip_tags((new MarkdownExtraParser())->transformMarkdown($this->title));
    }
}
