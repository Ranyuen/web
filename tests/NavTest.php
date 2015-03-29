<?php

class NavTest extends PHPUnit_Framework_TestCase
{
    public function testXmlFormat()
    {
        $doc = new DOMDocument();
        $doc->load('config/nav.xml');
        $this->assertTrue($doc->relaxNGValidate(__dir__.'/res/nav.rng'));
    }
}
