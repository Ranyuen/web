<?php

use Phpmig\Migration\Migration;

class AlterColumnsPhotoTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->table('photo', function ($t) {
            $t->dropColumn('color_r');
            $t->dropColumn('color_g');
            $t->dropColumn('color_b');
            $t->dropColumn('color_h');
            $t->dropColumn('color_s');
            $t->dropColumn('color_v');
            $t->string('color')->after('description_en')->nullable();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->table('photo', function ($t) {
            $t->dropColumn('color');
            $t->integer('color_r')->nullable();
            $t->integer('color_g')->nullable();
            $t->integer('color_b')->nullable();
            $t->integer('color_h')->nullable();
            $t->integer('color_s')->nullable();
            $t->integer('color_v')->nullable();
        });
    }
}
