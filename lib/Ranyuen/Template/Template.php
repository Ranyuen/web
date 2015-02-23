<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Template;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml;

/**
 * YAML+Jinja2+Markdown stack.
 *
 * YAML Front Matter + Jinja2 Template + Markdown.
 */
class Template
{
    /** @var array */
    public $params;

    /** @var string */
    private $content;
    /** @var Twig_Environment */
    private $engine;

    /**
     * Constructor.
     *
     * @param string $content
     */
    public function __construct($content, $params = [])
    {
        list($this->content, $matter) = $this->stripYamlFrontMatter($content);
        if ($matter) {
            $params = array_merge($matter, $params);
        }
        $this->params = $params;
        $loader = new \Twig_Loader_Array(['current' => '']);
        $loader->setTemplate('current', $this->content);
        $loader = new \Twig_Loader_Chain(
            [
                $loader,
                new \Twig_Loader_Filesystem(null),
            ]
        );
        $this->engine = new \Twig_Environment($loader);
    }

    public function registerHelper($helper)
    {
        $class = new \ReflectionClass(get_class($helper));
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $filter = new \Twig_SimpleFilter(
                $method->getName(),
                function () use ($helper, $method) {
                    return $method->invokeArgs($helper, func_get_args());
                }
            );
            $this->engine->addFilter($filter);
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        return (new MarkdownExtraParser())
            ->transformMarkdown($this->engine->render('current', $this->params));
    }

    /**
     * @param string $template Template string
     *
     * @return array list($content, $params)
     */
    private function stripYamlFrontMatter($template)
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
