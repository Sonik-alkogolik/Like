<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id(); // ID записи в таблице user_groups
            $table->unsignedBigInteger('user_id'); // ID пользователя из таблицы users
            $table->string('group_link'); // Ссылка на группу
            $table->string('group_link_id'); // ID группы, извлеченный из ссылки
            $table->timestamps();

            // Связь с таблицей users по user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_groups');
    }
};
