<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationProductTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutation_product', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned()->nullable(true);
            $table->integer('bar_id')->unsigned()->nullable(true);
            $table->integer('quantity')->unsigned()->nullable(false)->default(1);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onDelete('set null');
            $table->foreign('bar_id')
                ->references('id')
                ->on('bar')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mutation_product');
    }
}
