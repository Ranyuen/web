<?php

use Phpmig\Migration\Migration;

class CreateAdminTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('admin', function ($t) {
            $t->bigIncrements('id');
            $t->string('username', 100)->unique();
            $t->string('password', 255);
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('admin');
    }
}
