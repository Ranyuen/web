<?php

use Phpmig\Migration\Migration;

class ChangeArticleTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $schema = $this->get('schema');
        $schema->drop('article_tagging');
        $schema->drop('article_tag');
        $schema->drop('article');
        $schema->create('article', function ($t) {
            $t->bigIncrements('id');
            $t->string('path', 1023);
            $t->timestamps();
            $t->unique(['path']);
        });
        $schema->create('article_content', function ($t) {
            $t->bigIncrements('id');
            $t->string('lang', 3);
            $t->text('content');
            $t->bigInteger('article_id')->unsigned();
            $t->timestamps();
            $t->foreign('article_id')->references('id')->on('article')->onDelete('cascade');
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $schema = $this->get('schema');
        $schema->drop('article_content');
        $schema->drop('article');
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
}
