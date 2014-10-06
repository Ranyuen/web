<?php

use Phpmig\Migration\Migration;

class CreateContentTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('content', function ($t) {
            $t->bigIncrements('id');
            $t->text('title');
            $t->text('description')->nullable();
            $t->text('content');
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('content');
    }
}
