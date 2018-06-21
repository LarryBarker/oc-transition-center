<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class VariantsAddIsMaintenance extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('wwrf_mobile_variants', 'is_maintenance')) {
            return;
        }

        Schema::table('wwrf_mobile_variants', function($table)
        {
            $table->boolean('is_maintenance')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('wwrf_mobile_variants', 'is_maintenance'))
        {
          Schema::table('wwrf_mobile_variants', function($table)
          {
              $table->dropColumn('is_maintenance');
          });
        }
    }

}
