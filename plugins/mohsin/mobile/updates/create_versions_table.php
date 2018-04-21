<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateVersionsTable extends Migration
{

    public function up()
    {
        Schema::create('mohsin_mobile_versions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mohsin_mobile_versions');
    }

}
