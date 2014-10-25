<?php

use Phpmig\Migration\Migration;

class CreateArticleTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('article', function ($t) {
            $t->bigIncrements('id');
            $t->text('title');
            $t->text('description')->nullable();
            $t->text('content');
            $t->string('lang');
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('article');
    }
}
