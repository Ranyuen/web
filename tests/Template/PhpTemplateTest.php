<?php
require_once 'tests/res/TemplateTestResource.php';

use Ranyuen\Template\PhpTemplate;
use TemplateTestResource\GlobalHelper;
use TemplateTestResource\LocalHelper;

class PhpTemplateTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $t = new PhpTemplate();
        $template = 'Text <?php echo "PHP!"; ?>';
        $expected = 'Text PHP!';
        $t->parse($template);
        $this->assertEquals($expected, $t->render());
    }

    public function testRenderWithParams()
    {
        $t = new PhpTemplate();
        $template = 'Drink <?php echo $kinoko; ?>.';
        $params = ['kinoko' => 'ヒトヨタケ'];
        $expected = 'Drink ヒトヨタケ.';
        $t->parse($template);
        $this->assertEquals($expected, $t->render($params));
    }

    public function testRenderWithHelper()
    {
        $t = new PhpTemplate();
        $template = '<?php echo $dokutsurutake($kinoko); ?>.
<?php echo $kaentake($kinoko); ?>.';
        $params = ['kinoko' => ['ヒトヨタケ']];
        $expected = '死の天使 ヒトヨタケ.
症状: ヒトヨタケ.';
        $t->registerHelper(new GlobalHelper());
        $t->parse($template);
        $this->assertEquals($expected, $t->render($params, [new LocalHelper()]));
    }
}
