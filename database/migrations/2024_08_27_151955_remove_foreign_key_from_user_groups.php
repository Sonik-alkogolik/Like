<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyFromUserGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_groups', function (Blueprint $table) {
            // Удалите внешний ключ
            $table->dropForeign(['user_id_vk']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_groups', function (Blueprint $table) {
            // Если потребуется восстановить внешний ключ, добавьте его снова
            $table->foreign('user_id_vk')->references('id')->on('users')->onDelete('cascade');
        });
    }
}