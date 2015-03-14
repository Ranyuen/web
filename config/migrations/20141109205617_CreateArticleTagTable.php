<?php

use Phpmig\Migration\Migration;

class CreateArticleTagTable extends Migration
{
    /**
     * Do the migration.
     */
    public function up()
    {
        $this->get('schema')->create('article_tag', function ($t) {
            $t->bigIncrements('id');
            $t->string('name_ja', 100)->unique();
            $t->string('name_en', 100)->unique();
            $t->timestamps();
        });
        $this->get('schema')->create('article_tagging', function ($t) {
            $t->bigIncrements('id');
            $t->bigInteger('article_id')->references('id')->on('article');
            $t->bigInteger('article_tag_id')->references('id')->on('article_tag');
            $t->boolean('is_primary')->default(false);
            $t->unique(['article_id', 'article_tag_id']);
        });
    }

    /**
     * Undo the migration.
     */
    public function down()
    {
        $this->get('schema')->drop('article_tagging');
        $this->get('schema')->drop('article_tag');
    }
}
