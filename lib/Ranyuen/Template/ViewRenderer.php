<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Template;

/**
 * Render view.
 */
class ViewRenderer
{
    /**
     * Template directory.
     *
     * @var string
     */
    private $dir;
    /**
     * Layout file name.
     *
     * @var string
     */
    private $layout;
    private $helpers = [];

    /**
     * Constructor.
     *
     * @param string $templateDir Template directory.
     */
    public function __construct($templateDir)
    {
        $this->dir = $templateDir;
    }

    /**
     * Set layout file name.
     *
     * @param string $templateName Jinja2 HTML file path.
     *
     * @return void
     */
    public function setLayout($templateName)
    {
        if (is_null($templateName)) {
            $this->layout = null;

            return;
        }
        if (is_file("$this->dir/$templateName.html")) {
            $this->layout = file_get_contents("$this->dir/$templateName.html");
        }
    }

    public function addHelper($helper)
    {
        $this->helpers[] = $helper;
    }

    /**
     * Render.
     *
     * @param string   $templateName Jinja2 Markdown file path.
     * @param array    $params       Template params.
     * @param object[] $helpers      Template helpers.
     *
     * @return string
     */
    public function render($templateName, $params = [], $helpers = [])
    {
        if (is_file("$this->dir/$templateName.md")) {
            $content = file_get_contents("$this->dir/$templateName.md");
        } elseif (is_file("$this->dir/$templateName.markdown")) {
            $content = file_get_contents("$this->dir/$templateName.markdown");
        } else {
            throw new TempalteFileNotFoundException($templateName);
        }

        return $this->renderContent($content, $params, $helpers);
    }

    public function renderContent($content, $params = [], $helpers = [])
    {
        $template = new Template($content, $params, $this->dir);
        $output = $this->renderRawContent($template, $params, $helpers);
        if (!$this->layout) {
            return $output;
        }
        $params['content'] = $output;
        $params = array_merge($template->params, $params);

        return $this->renderRawContent($this->layout, $params, $helpers);
    }

    private function renderRawContent($content, $params = [], $helpers = [])
    {
        if (!($content instanceof Template)) {
            $content = new Template($content, $params, $this->dir);
        }
        foreach (array_merge($helpers, $this->helpers) as $helper) {
            $content->addHelper($helper);
        }

        return $content->render();
    }
}
