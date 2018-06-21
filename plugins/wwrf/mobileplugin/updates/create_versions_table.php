<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateVersionsTable extends Migration
{

    public function up()
    {
        Schema::create('wwrf_mobile_versions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wwrf_mobile_versions');
    }

}
