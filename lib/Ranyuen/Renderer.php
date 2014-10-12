<?php
namespace Ranyuen;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml;

/**
 * YAML+Liquid+Markdown stack.
 *
 * YAML Front Matter + Liquid Template + Markdown.
 */
class Renderer
{
    private $_templates_path = 'view';
    /** @var Liquid\Template */
    private $_template;
    /** @var string */
    private $_layout = null;

    /**
     * @param string $templates_path
     */
    public function __construct($templates_path)
    {
        $this->_templates_path = $templates_path;
        $this->_template = new Template\LiquidTemplate($templates_path);
    }

    /**
     * @param  string   $template_name Liquid HTML file path.
     * @return Renderer
     */
    public function setLayout($template_name)
    {
        $dir = $this->_templates_path;
        if (is_file("$dir/$template_name.html")) {
            $this->_layout = file_get_contents("$dir/$template_name.html");
        }

        return $this;
    }

    /**
     * @param  object   $helper
     * @return Renderer
     */
    public function addHelper($helper)
    {
        $this->_template->registerHelper($helper);

        return $this;
    }

    /**
     * @param  string $template_name Liquid Markdown file path.
     * @param  array  $params
     * @return string
     */
    public function render($template_name, $params = [])
    {
        $dir = $this->_templates_path;
        if (is_file("$dir/$template_name.md")) {
            $template = file_get_contents("$dir/$template_name.md");
        } elseif (is_file("$dir/$template_name.markdown")) {
            $template = file_get_contents("$dir/$template_name.markdown");
        } else {
            return false;
        }
        if ($this->_layout) {
            list($content, $front_matter) =
                $this->stripYamlFromtMatter($template);
            if ($front_matter) {
                $params = array_merge($front_matter, $params);
            }
            $params['content'] = (new MarkdownExtraParser())
                ->transformMarkdown($this->renderTemplate($content, $params));

            return $this->renderTemplate($this->_layout, $params);
        } else {
            return (new MarkdownExtraParser())
                ->transformMarkdown($this->renderTemplate($template, $params));
        }
    }

    /**
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function renderTemplate($template, $params = [])
    {
        list($template, $fromt_matter) =
            $this->stripYamlFromtMatter($template);
        if ($fromt_matter) {
            $params = array_merge($fromt_matter, $params);
        }

        return $this->_template->parse($template)->render($params);
    }

    /**
     * @param  string $template
     * @return array  list($content, $params)
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
            for (; $i < $iz && ! preg_match('/^-{3,}\s*$/', $lines[$i]); ++ $i) {
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
