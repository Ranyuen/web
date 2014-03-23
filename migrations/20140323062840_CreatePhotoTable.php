<?php

use Phpmig\Migration\Migration;

class CreatePhotoTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = 'create table photo (
    id text,
    description_ja text,
    description_en text,
    color_r integer,
    color_g integer,
    color_b integer,
    color_h integer,
    color_s integer,
    color_v integer,
    species_name text,
    product_name text,
    primary key (id)
)';
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $sql = 'drop table photo';
        $container = $this->getContainer();
        $container['db']->query($sql);
    }
}
