<?php

use Phpmig\Migration\Migration;

class CreateExamQuestionTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('exam_question', function ($t) {
            $t->bigIncrements('id');
            $t->text('question');
            $t->string('type', 25);
            $t->text('description')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('exam_question');
    }
}
