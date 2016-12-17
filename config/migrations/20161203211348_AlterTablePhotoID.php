<?php

use Phpmig\Migration\Migration;

class AlterTablePhotoID extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $schema = $this->get('schema');
        $schema->drop('photo');
        $schema->create('photo', function ($t) {
            $t->bigIncrements('id');
            $t->string('uuid');
            $t->text('description_ja')->nullable();
            $t->text('description_en')->nullable();
            $t->string('color')->nullable();
            $t->string('species_name')->nullable();
            $t->string('product_name')->nullable();
            $t->integer('width')->nullable();
            $t->integer('height')->nullable();
            $t->boolean('is_top')->default(false)->nullable();
            $t->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $schema = $this->get('schema');
        $schema->drop('photo');
        $schema->create('photo', function ($t) {
            $t->string('id');
            $t->text('description_ja')->nullable();
            $t->text('description_en')->nullable();
            $t->string('color')->nullable();
            $t->string('species_name')->nullable();
            $t->string('product_name')->nullable();
            $t->integer('width')->nullable();
            $t->integer('height')->nullable();
            $t->boolean('is_top')->default(false)->nullable();
            $t->primary('id');
            $t->timestamps();
        });
    }
}
