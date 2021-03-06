<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomyTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('economy', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('community_id')->unsigned();
            $table->string('name', 255)->nullable();
            $table->timestamps();

            $table->foreign('community_id')
                ->references('id')
                ->on('community')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('economy');
    }
}
