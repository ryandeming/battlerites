<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battlerites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hero_id');
            $table->string('hero_name');
            $table->string('hotkey');
            $table->string('name');
            $table->string('description');
            $table->string('category');
            $table->string('img_src');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battlerites');
    }
}
