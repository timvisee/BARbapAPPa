<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable(true);
            // TODO: is a type field used for custom products?
            $table->integer('type')->unsigned()->nullable(false);
            $table->string('name')->nullable(false);
            // TODO: categories?
            $table->boolean('enabled')->default(true)->nullable(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');

            // TODO: if custom product and user is deleted, delete product as well
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product');
    }
}
