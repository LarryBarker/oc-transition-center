<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateVariantsTable extends Migration
{

    public function up()
    {
        Schema::create('mohsin_mobile_variants', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('app_id')->unsigned()->nullable()->index();
            $table->string('package')->unique();
            $table->integer('platform_id')->unsigned()->nullable()->index();
            $table->string('description')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mohsin_mobile_variants');
    }

}
