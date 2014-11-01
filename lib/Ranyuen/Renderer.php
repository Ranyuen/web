<?php
/**
 * YAML+Jinja2+Markdown stack
 */
namespace Ranyuen;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml;

/**
 * YAML+Jinja2+Markdown stack
 *
 * YAML Front Matter + Jinja2 Template + Markdown.
 */
class Renderer
{
    private $templatesPath = 'view';
    /** @var Template\TwigTemplate */
    private $template;
    /** @var string */
    private $layout = null;

    /**
     * @param string $templatesPath Template directory
     */
    public function __construct($templatesPath)
    {
        $this->templatesPath = $templatesPath;
        $this->template = new Template\TwigTemplate($templatesPath);
    }

    /**
     * @param string $templateName Jinja2 HTML file path
     *
     * @return Renderer
     */
    public function setLayout($templateName)
    {
        $dir = $this->templatesPath;
        if (is_file("$dir/$templateName.html")) {
            $this->layout = file_get_contents("$dir/$templateName.html");
        }

        return $this;
    }

    /**
     * @param object $helper Template helper
     *
     * @return Renderer
     */
    public function addHelper($helper)
    {
        $this->template->registerHelper($helper);

        return $this;
    }

    /**
     * @param string $templateName Jinja2 Markdown file path
     * @param array  $params       Template params
     *
     * @return string
     */
    public function render($templateName, $params = [])
    {
        $dir = $this->templatesPath;
        if (is_file("$dir/$templateName.md")) {
            $template = file_get_contents("$dir/$templateName.md");
        } elseif (is_file("$dir/$templateName.markdown")) {
            $template = file_get_contents("$dir/$templateName.markdown");
        } else {
            return false;
        }
        if ($this->layout) {
            list($content, $frontMatter)
                = $this->stripYamlFromtMatter($template);
            if ($frontMatter) {
                $params = array_merge($frontMatter, $params);
            }
            $params['content'] = (new MarkdownExtraParser())
                ->transformMarkdown($this->renderTemplate($content, $params));

            return $this->renderTemplate($this->layout, $params);
        } else {
            return (new MarkdownExtraParser())
                ->transformMarkdown($this->renderTemplate($template, $params));
        }
    }

    /**
     * @param string $template Template string
     * @param array  $params   Template params
     *
     * @return string
     */
    public function renderTemplate($template, $params = [])
    {
        list($template, $fromtMatter)
            = $this->stripYamlFromtMatter($template);
        if ($fromtMatter) {
            $params = array_merge($fromtMatter, $params);
        }

        return $this->template->parse($template)->render($params);
    }

    /**
     * @param string $template Template string
     *
     * @return array list($content, $params)
     */
    private function stripYamlFromtMatter($template)
    {
        $front = '';
        $content = '';
        $lines = explode("\n", $template);
        $i = 0;
        $iz = count($lines);
        if (preg_match('/^-{3,}\s*$/', $lines[0])) {
            $i = 1;
            for (; $i < $iz; ++$i) {
                if (preg_match('/^-{3,}\s*$/', $lines[$i])) {
                    break;
                }
                $front .= "$lines[$i]\n";
            }
            ++$i;
        }
        $params = (new Yaml\Parser())->parse($front);
        for (; $i < $iz; ++$i) {
            $content .= "$lines[$i]\n";
        }

        return [$content, $params];
    }
}
