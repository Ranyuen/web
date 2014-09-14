<?php
namespace Ranyuen;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml;

class Renderer
{
    /** @type array */
    private $config;
    /** @type string */
    private $layout = null;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param  string   $template_name
     * @return Renderer
     */
    public function setLayout($tamplate_name)
    {
        $this->layout = file_get_contents(
            "{$this->config['templates.path']}/$tamplate_name.php");

        return $this;
    }

    /**
     * @param  string $template_name
     * @param  array  $params
     * @return string
     */
    public function render($template_name, $params = [])
    {
        $template_type = $this->detectTemplateType($template_name);
        $filepath = "{$this->config['templates.path']}/$template_name.$template_type";
        if (! is_file($filepath)) { return false; }
        $template = file_get_contents($filepath);
        switch ($template_type) {
            case 'php':
                return $this->renderPhpTemplateWithLayout($template, $params);
            case 'markdown':
                return $this->renderMarkdownTemplateWithLayout($template, $params);
        }
    }

    /**
     * @param  string $template
     * @param  array  $__params
     * @return string
     */
    public function renderTemplate($template, $__params = [])
    {
        list($template, $fromtMatter) = $this->stripYamlFromtMatter($template);
        if ($fromtMatter) {
            $__params = array_merge($fromtMatter, $__params);
        }
        $__params['h'] = new Helper($this->config);
        $render = function () use ($__params) {
            foreach (func_get_arg(1) as $__k => $__v) {
                ${$__k} = $__v;
            }
            unset($__k);
            unset($__v);
            ob_start();
            eval('?>' . func_get_arg(0));

            return ob_get_clean();
        };
        $render = $render->bindTo(null);

        return $render($template, $__params);
    }

    /**
     * @param  string $template_name
     * @return string 'php' or 'markdown'
     */
    private function detectTemplateType($template_name)
    {
        $template_type = 'php';
        $dir = dirname("{$this->config['templates.path']}/$template_name");
        if (! is_dir($dir)) { return 'php'; }
        if ($handle = opendir($dir)) {
            $regex = '/^(?:' . basename($template_name) . ')\.(php|markdown)$/';
            while (false !== ($file = readdir($handle))) {
                $matches = [];
                if (is_file("$dir/$file") &&
                    preg_match($regex, $file, $matches)) {
                    $template_type = $matches[1];
                    break;
                }
            }
        }

        return $template_type;
    }

    /**
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    private function renderPhpTemplateWithLayout($template, $params)
    {
        if ($this->layout) {
            list($content, $frontMatter) = $this->stripYamlFromtMatter($template);
            if ($frontMatter) {
                $params = array_merge($frontMatter, $params);
            }
            $params['content'] = $content;

            return $this->renderTemplate($this->layout, $params);
        } else {
            return $this->renderTemplate($template, $params);
        }
    }

    /**
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    private function renderMarkdownTemplateWithLayout($template, $params)
    {
        if ($this->layout) {
            list($content, $frontMatter) = $this->stripYamlFromtMatter($template);
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
            ++ $i;
        }
        $params = (new Yaml\Parser())->parse($front);
        for (; $i < $iz; ++ $i)
            $content .= "$lines[$i]\n";

        return [$content, $params];
    }
}
