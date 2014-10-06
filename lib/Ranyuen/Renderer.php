<?php
namespace Ranyuen;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml;

class Renderer
{
    /** @var array */
    private $_config;
    /** @var string */
    private $_layout = null;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * @param  string   $templateName
     * @return Renderer
     */
    public function setLayout($templateName)
    {
        $this->_layout = file_get_contents(
            "{$this->_config['templates.path']}/$templateName.php"
        );

        return $this;
    }

    /**
     * @param  string $templateName
     * @param  array  $params
     * @return string
     */
    public function render($templateName, $params = [])
    {
        $templateType = $this->detectTemplateType($templateName);
        $filepath = "{$this->_config['templates.path']}/$templateName.$templateType";
        if (!is_file($filepath)) {
            return false;
        }
        $template = file_get_contents($filepath);
        switch ($templateType) {
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
        $__params['h'] = new Helper($this->_config);
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
     * @param  string $templateName
     * @return string 'php' or 'markdown'
     */
    private function detectTemplateType($templateName)
    {
        $templateType = 'php';
        $dir = dirname("{$this->_config['templates.path']}/$templateName");
        if (!is_dir($dir)) {
            return 'php';
        }
        if ($handle = opendir($dir)) {
            $regex = '/^(?:' . basename($templateName) . ')\.(php|markdown)$/';
            while (false !== ($file = readdir($handle))) {
                $matches = [];
                if (is_file("$dir/$file") &&
                    preg_match($regex, $file, $matches)) {
                    $templateType = $matches[1];
                    break;
                }
            }
        }

        return $templateType;
    }

    /**
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    private function renderPhpTemplateWithLayout($template, $params)
    {
        if ($this->_layout) {
            list($content, $frontMatter) =
                $this->stripYamlFromtMatter($template);
            if ($frontMatter) {
                $params = array_merge($frontMatter, $params);
            }
            $params['content'] = $content;

            return $this->renderTemplate($this->_layout, $params);
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
        if ($this->_layout) {
            list($content, $frontMatter) =
                $this->stripYamlFromtMatter($template);
            if ($frontMatter) {
                $params = array_merge($frontMatter, $params);
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
