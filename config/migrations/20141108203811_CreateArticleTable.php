<?php

use Phpmig\Migration\Migration;

class CreateArticleTable extends Migration
{
    /**
     * Do the migration.
     */
    public function up()
    {
        $this->get('schema')->create('article', function ($t) {
            $t->bigIncrements('id');
            $t->string('title', 1023);
            $t->string('description', 1023)->nullable();
            $t->text('content');
            $t->string('url', 255);
            $t->string('lang', 3)->default('ja');
            $t->timestamps();
            $t->unique(['url', 'lang']);
        });
    }

    /**
     * Undo the migration.
     */
    public function down()
    {
        $this->get('schema')->drop('article');
    }
}
