<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->text('campaign_name')->nullable();
            $table->text('campaign_id')->nullable();
            $table->integer('customer_id');
            $table->integer('link_campaign')->nullable();
            $table->boolean('is_smart')->default(false);
            $table->integer('phase')->default('0');
            $table->boolean('active')->default(true);
            $table->string('last_check_date')->nullable();
            $table->string('status')->nullable();
            $table->text('startDate')->nullable();
            $table->text('endDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}