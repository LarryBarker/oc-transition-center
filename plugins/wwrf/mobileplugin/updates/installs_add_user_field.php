<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class InstallsAddUserField extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('wwrf_mobile_installs', 'user_id')) {
            return;
        }

        Schema::table('wwrf_mobile_installs', function($table) {
            $table->integer('user_id')->after('instance_id')->unsigned()->nullable()->index();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('wwrf_mobile_installs', 'user_id'))
        {
            Schema::table('wwrf_mobile_installs', function($table) {
                $table->dropColumn(['user_id']);
            });
        }
    }
}
