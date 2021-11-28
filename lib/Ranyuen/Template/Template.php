<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Template;

use Michelf\Markdown;
use Symfony\Component\Yaml;

/**
 * YAML+Jinja2+Markdown stack.
 *
 * YAML Front Matter + Jinja2 Template + Markdown.
 */
class Template
{
    /**
     * Params.
     *
     * @var array
     */
    public $params;

    /**
     * Twig engine.
     *
     * @var Twig_Environment
     */
    private $engine;

    /**
     * Constructor.
     *
     * @param string $content     Template string.
     * @param array  $params      Parameters.
     * @param string $templateDir Template directory (for layout rendering).
     */
    public function __construct($content, $params = [], $templateDir = null)
    {
        list($content, $matter) = $this->stripYamlFrontMatter($content);
        if ($matter) {
            $params = array_merge($matter, $params);
        }
        $this->params = $params;
        $loader = new \Twig_Loader_Array(['current' => '']);
        $loader->setTemplate('current', $content);
        $loader = new \Twig_Loader_Chain(
            [
                $loader,
                new \Twig_Loader_Filesystem($templateDir),
            ]
        );
        $this->engine = new \Twig_Environment($loader);
    }

    /**
     * Register a helper instance.
     *
     * @param object $helper Helper class instance.
     *
     * @return void
     */
    public function addHelper($helper)
    {
        $class = new \ReflectionClass(get_class($helper));
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $this->engine->addFilter(
                new \Twig_SimpleFilter(
                    $method->getName(),
                    function () use ($helper, $method) {
                        return $method->invokeArgs($helper, func_get_args());
                    }
                )
            );
        }
    }

    /**
     * Render the template.
     *
     * @return string
     */
    public function render()
    {
        $content = $this->engine->render('current', $this->params);
        if (preg_match('/\A<!DOCTYPE /', $content)) {
            return $content;
        } else {
            
            return Markdown::defaultTransform($content);
        }
    }

    /**
     * Strip YAML fromt matter from the content.
     *
     * @param string $template Template string.
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
