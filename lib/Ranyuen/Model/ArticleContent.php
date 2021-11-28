<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Model;

use League\CommonMark\CommonMarkConverter;
use Illuminate\Database\Eloquent;
use Ranyuen\Template\Template;

/**
 * ArticleContent model.
 */
class ArticleContent extends Eloquent\Model
{
    protected $table    = 'article_content';
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
     * Strip tags in the title.
     *
     * @return string
     */
    public function plainTitle()
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        
        return strip_tags($converter->convertToHtml($this->title));
    }
}
