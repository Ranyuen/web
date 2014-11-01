<?php
use \Ranyuen\Renderer;

class RendererTest extends PHPUnit_Framework_TestCase
{
    public function testRenderTemplate()
    {
        $renderer = new Renderer([]);
        $template = 'First line.
{{ "Second line." }}
Third line.';
        $expected = 'First line.
Second line.
Third line.
';
        $this->assertEquals($expected, $renderer->renderTemplate($template));
    }

    public function testRenderTemplateWithYamlFrontMatter()
    {
        $renderer = new Renderer([]);
        $template = '---
front1: どんぐりと山猫
front2: オツベルと象
front2: ペンネンネンネンネン・ネネムの伝記
---
First line.
{{front1}} v.s. {{front2}}';
        $expected = 'First line.
どんぐりと山猫 v.s. オツベルと象
';
        $this->assertEquals($expected, $renderer->renderTemplate($template));
    }

    public function testRenderTemplateWithParams()
    {
        $renderer = new Renderer([]);
        $template = '{{left}} x {{right}}';
        $params = [
            'left'  => 'カムパネルラ',
            'right' => 'ジョバンニ',
        ];
        $expected = 'カムパネルラ x ジョバンニ
';
        $this->assertEquals($expected, $renderer->renderTemplate($template, $params));
    }

    public function testRenderTemplateWithYamlFrontMatterAndParams()
    {
        $renderer = new Renderer([]);
        $template = '---
left:  カムパネルラ
right: ジョバンニ
---
{{left}} x {{right}}';
        $params = ['right' => 'グスコーブドリ'];
        $expected = 'カムパネルラ x グスコーブドリ
';
        $this->assertEquals($expected, $renderer->renderTemplate($template, $params));
    }
}
