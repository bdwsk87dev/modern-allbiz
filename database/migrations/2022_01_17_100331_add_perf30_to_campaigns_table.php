<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerf30ToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
            $table->float('cost_30', 9,2)->nullable();
            $table->float('conv_30', 9, 2)->nullable();
            $table->float('conv_value_30', 9, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
            $table->dropColumn('cost_30');
            $table->dropColumn('conv_30');
            $table->dropColumn('conv_value_30');
        });
    }
}
