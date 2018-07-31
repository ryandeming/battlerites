<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_api_id');
            $table->string('hero_name');
            $table->integer('result');
            $table->integer('damage_done');
            $table->integer('damage_received');
            $table->integer('healing_done');
            $table->integer('healing_received');
            $table->integer('disables_done');
            $table->integer('disables_received');
            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('score');
            $table->string('date');
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
        Schema::dropIfExists('player_matches');
    }
}
