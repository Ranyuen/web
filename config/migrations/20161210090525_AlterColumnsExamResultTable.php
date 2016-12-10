<?php

use Phpmig\Migration\Migration;

class AlterColumnsExamResultTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->get('schema')->table('photo', function ($t) {
            $t->string('passwd')->after('type')->nullable();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->get('schema')->table('photo', function ($t) {
            $t->dropColumn('passwd');
        });
    }
}
