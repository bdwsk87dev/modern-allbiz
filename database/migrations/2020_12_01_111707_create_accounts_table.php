<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {

            // Fields
            $table->id();
            $table->text('account_name')->nullable();
            $table->longText('description')->nullable();
            $table->longText('email')->nullable();

            // Api data
            $table->text('developer_token')->nullable();
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('refresh_token')->nullable();

            // Параметр для тестирования
            $table->boolean('testing')->default(false);

            // Проверка 7 дней
            $table->boolean('seven_days')->default(true);

            // Settings
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('accounts');
    }
}
