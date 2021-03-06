<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('password', 4096)->nullable();
            $table->integer('role')->unsigned()->default(0);
            $table->rememberToken();
            $table->string('locale')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user');
    }
}
