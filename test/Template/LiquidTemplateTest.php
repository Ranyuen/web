<?php
require_once 'test/res/TemplateTestResource.php';

use Ranyuen\Template\LiquidTemplate;
use TemplateTestResource\GlobalHelper;
use TemplateTestResource\LocalHelper;

class LiquidTemplateTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $t = new LiquidTemplate();
        $template = 'Text {{"Liquid."}}';
        $expected = 'Text Liquid.';
        $t->parse($template);
        $this->assertEquals($expected, $t->render());
    }

    public function testRenderWithParams()
    {
        $t = new LiquidTemplate();
        $template = 'Drink {{kinoko}}.';
        $params = ['kinoko' => 'ヒトヨタケ'];
        $expected = 'Drink ヒトヨタケ.';
        $t->parse($template);
        $this->assertEquals($expected, $t->render($params));
    }

    public function testRenderWithHelper()
    {
        $t = new LiquidTemplate();
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
