<?php
require_once 'test/res/TemplateTestResource.php';

use Ranyuen\Template\TwigTemplate;
use TemplateTestResource\GlobalHelper;
use TemplateTestResource\LocalHelper;

class TwigTemplateTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $t = new TwigTemplate();
        $template = 'Text {{"Twig."}}';
        $expected = 'Text Twig.';
        $t->parse($template);
        $this->assertEquals($expected, $t->render());
    }

    public function testRenderWithParams()
    {
        $t = new TwigTemplate();
        $template = 'Drink {{kinoko}}.';
        $params = ['kinoko' => 'ヒトヨタケ'];
        $expected = 'Drink ヒトヨタケ.';
        $t->parse($template);
        $this->assertEquals($expected, $t->render($params));
    }

    public function testRenderWithHelper()
    {
        $t = new TwigTemplate();
        $template = '{{kinoko | dokutsurutake}}.
{{kinoko | kaentake}}.';
        $params = ['kinoko' => ['ヒトヨタケ']];
        $expected = '死の天使 ヒトヨタケ.
症状: ヒトヨタケ.';
        $t->registerHelper(new GlobalHelper());
        $t->parse($template);
        $this->assertEquals($expected, $t->render($params, new LocalHelper()));
    }
}
