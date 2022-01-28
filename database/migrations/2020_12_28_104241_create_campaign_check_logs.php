<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignCheckLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_check_logs', function (Blueprint $table) {
            $table->id();
            $table->text('campaign_id'); // Это поле id в таблице campaigns
            $table->text('check_time'); // Это время проверки кампании
            $table->integer('block'); // Это блок с которого началась проверка
            $table->integer('stage'); // Это фрагмент блока тестирования
            $table->string('current_status'); // Это вся информация о компании
            $table->boolean('resoult'); // Результат выполнения
            $table->text('message'); // Текст об ошибках
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
        Schema::dropIfExists('campaign_check_logs');
    }
}
