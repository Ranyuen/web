<?php

use Phpmig\Migration\Migration;

class AlterColumnsPhotoTable2 extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->table('photo', function($t) {
            $t->boolean('is_top')->default(false)->after('height')->nullable();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->table('photo', function($t) {
            $t->dropColumn('is_top');
        });
    }
}
