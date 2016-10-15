<?php

use Phpmig\Migration\Migration;

class CreateExamResultTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('exam_result', function ($t) {
            $t->bigIncrements('id');
            $t->string('user_name', 20);
            $t->integer('points');
            $t->string('type', 25);
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('exam_result');
    }
}
