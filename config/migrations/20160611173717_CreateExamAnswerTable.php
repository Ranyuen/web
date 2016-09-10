<?php

use Phpmig\Migration\Migration;

class CreateExamAnswerTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('exam_answer', function ($t) {
            $t->bigIncrements('id');
            $t->bigInteger('question_id')->unsigned();
            $t->text('choice');
            $t->boolean('is_correct')->default(false);
            $t->timestamps();
        });
        $this->get('schema')->table('exam_answer', function ($t) {
            $t->foreign('question_id')->references('id')->on('exam_question');
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('exam_answer');
    }
}
