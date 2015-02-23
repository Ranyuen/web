<?php
require_once 'tests/res/TemplateTestResource.php';

use Ranyuen\Template\Template;
use TemplateTestResource\GlobalHelper;

class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testRenderTemplate()
    {
        $source = 'First line.
{{ "Second line." }}
Third line.';
        $expected = '<p>First line.
Second line.
Third line.</p>
';
        $template = new Template($source);
        $this->assertEquals($expected, $template->render());
    }

    public function testRenderTemplateWithYamlFrontMatter()
    {
        $source = '---
front1: どんぐりと山猫
front2: オツベルと象
front2: ペンネンネンネンネン・ネネムの伝記
---
First line.
{{front1}} v.s. {{front2}}';
        $expected = '<p>First line.
どんぐりと山猫 v.s. オツベルと象</p>
';
        $template = new Template($source);
        $this->assertEquals($expected, $template->render());
    }

    public function testRenderTemplateWithParams()
    {
        $source = '{{left}} x {{right}}';
        $params = [
            'left'  => 'カムパネルラ',
            'right' => 'ジョバンニ',
        ];
        $expected = '<p>カムパネルラ x ジョバンニ</p>
';
        $template = new Template($source, $params);
        $this->assertEquals($expected, $template->render());
    }

    public function testRenderTemplateWithYamlFrontMatterAndParams()
    {
        $source = '---
left:  カムパネルラ
right: ジョバンニ
---
{{left}} x {{right}}';
        $params = ['right' => 'グスコーブドリ'];
        $expected = '<p>カムパネルラ x グスコーブドリ</p>
';
        $template = new Template($source, $params);
        $this->assertEquals($expected, $template->render());
    }

    public function testRenderWithHelper()
    {
        $source = '{{kinoko | dokutsurutake}}.
{{kinoko | kaentake}}.';
        $params = ['kinoko' => ['ヒトヨタケ']];
        $expected = '<p>死の天使 ヒトヨタケ.
症状: ヒトヨタケ.</p>
';
        $template = new Template($source, $params);
        $template->registerHelper(new GlobalHelper());
        $this->assertEquals($expected, $template->render());
    }
}
