<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->text('customer_name')->nullable();
            $table->integer('account_id');
            $table->text('customer_id')->nullable();
            $table->text('parent_customer_id')->nullable();
            $table->boolean('can_manage_clients')->default(false);
            $table->boolean('active')->default(true);
            $table->string('last_check_date')->nullable();
            $table->string('start_budget')->nullable();
            $table->string('max_budget')->nullable();
            $table->boolean('has_new_campaigns')->default(false);
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
        //
        Schema::dropIfExists('customers');
    }
}
