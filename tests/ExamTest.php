<?php

use Ranyuen\Model\ExamQuestion;

use Illuminate\Database\Capsule\Manager as DB;

class ExamTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->configureDatabase();
    }

    protected function configureDatabase() {
        $db = new DB;
        $db->addConnection(array(
            'host'      => 'localhost',
            'driver'    => 'mysql',
            'database'  => 'ranyuen_production',
            'username'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ));
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    public function testReadJSON() {
        ExamQuestion::readFromJSON('assets/exam/easy.json');
        ExamQuestion::readFromJSON('assets/exam/hard.json');
        ExamQuestion::readFromJSON('assets/exam/expert.json');
    }
}
