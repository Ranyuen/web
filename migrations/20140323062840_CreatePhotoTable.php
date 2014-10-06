<?php

use Phpmig\Migration\Migration;

class CreatePhotoTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->create('photo', function ($t) {
            $t->string('id');
            $t->text('description_ja')->nullable();
            $t->text('description_en')->nullable();
            $t->integer('color_r')->nullable();
            $t->integer('color_g')->nullable();
            $t->integer('color_b')->nullable();
            $t->integer('color_h')->nullable();
            $t->integer('color_s')->nullable();
            $t->integer('color_v')->nullable();
            $t->string('species_name')->nullable(); // 種名 (Calanthe, Ponerorchis)
            $t->string('product_name')->nullable(); // 品種名
            $t->integer('width')->nullable();
            $t->integer('height')->nullable();
            $t->primary('id');
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->drop('photo');
    }
}
