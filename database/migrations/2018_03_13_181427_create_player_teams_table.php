<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_api_id');
            $table->string('name');
            $table->string('league');
            $table->string('league_id');
            $table->string('division');
            $table->string('rating');
            $table->string('type');
            $table->integer('season');
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
        Schema::dropIfExists('player_teams');
    }
}
