<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Template;

/**
 */
class ViewRenderer
{
    /** @var string */
    private $dir;
    /** @var string */
    private $layout;
    private $helpers = [];

    /**
     * @param string $templateDir Template directory.
     */
    public function __construct($templateDir)
    {
        $this->dir = $templateDir;
    }

    /**
     * @param string $templateName Jinja2 HTML file path.
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
        $output = $this->renderContent($content, $params, $helpers);
        if (!$this->layout) {
            return $output;
        }
        $params['content'] = $output;

        return $this->renderContent($this->layout, $params, $helpers);
    }

    public function renderContent($content, $params = [], $helpers = [])
    {
        $template = new Template($content, $params, $this->dir);
        foreach (array_merge($helpers, $this->helpers) as $helper) {
            $template->addHelper($helper);
        }

        return $template->render();
    }
}
