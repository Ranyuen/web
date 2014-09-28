<?php
use \Ranyuen\Color;

class ColorTest extends PHPUnit_Framework_TestCase
{
    private $set = [
        [[  0,   0,   0], [  0,   0,   0]], //black
        [[255, 255, 255], [  0,   0, 100]], // white
        [[255,   0,   0], [  0, 100, 100]], // red
        [[  0, 255,   0], [120, 100, 100]], // lime
        [[  0,   0, 255], [240, 100, 100]], // blue
        [[255, 255,   0], [ 60, 100, 100]], // yellow
        [[  0, 255, 255], [180, 100, 100]], // cyan
        [[255,   0, 255], [300, 100, 100]], // magenta
        [[192, 192, 192], [  0,   0,  75]], // silver
        [[128, 128, 128], [  0,   0,  50]], // gray
        [[128,   0,   0], [  0, 100,  50]], // maroon
        [[128, 128,   0], [ 60, 100,  50]], // olive
        [[  0, 128,   0], [120, 100,  50]], // green
        [[128,   0, 128], [300, 100,  50]], // purple
        [[  0, 128, 128], [180, 100,  50]], // teal
        [[  0,   0, 128], [240, 100,  50]], // navy
    ];

    public function testRgbToHsvIsCorrect()
    {
        array_walk($this->set, function ($set) {
            list($r, $g, $b) = $set[0];
            list($h, $s, $v) = $set[1];
            $c = (new Color())->fromRgb($r, $g, $b);
            $this->assertEquals([$h, $s, $v], $c->hsv());
        });
    }

    public function testHsvToRgbIsCorrect()
    {
        array_walk($this->set, function ($set) {
            list($r, $g, $b) = $set[0];
            list($h, $s, $v) = $set[1];
            $c = (new Color())->fromHsv($h, $s, $v);
            $this->assertEquals([$r, $g, $b], $c->rgb());
        });
    }

    public function testHsvReturnsEqualHsv()
    {
        array_walk($this->set, function ($set) {
            list($h, $s, $v) = $set[1];
            $c = (new Color())->fromHsv($h, $s, $v);
            $this->assertEquals([$h, $s, $v], $c->hsv());
        });
    }
}
