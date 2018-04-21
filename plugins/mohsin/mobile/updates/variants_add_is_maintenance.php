<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class VariantsAddIsMaintenance extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('mohsin_mobile_variants', 'is_maintenance')) {
            return;
        }

        Schema::table('mohsin_mobile_variants', function($table)
        {
            $table->boolean('is_maintenance')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('mohsin_mobile_variants', 'is_maintenance'))
        {
          Schema::table('mohsin_mobile_variants', function($table)
          {
              $table->dropColumn('is_maintenance');
          });
        }
    }

}
