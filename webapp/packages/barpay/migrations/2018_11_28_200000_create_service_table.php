<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('services', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->morphs('serviceable');
            $table->boolean('enabled')->setNullable(false)->default(true);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->boolean('deposit')->nullable(false);
            $table->boolean('withdraw')->nullable(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('restrict');

            $table->index(['serviceable_id', 'serviceable_type']);

            // TODO: add a field for supported currency/currencies by this service
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('services');
    }
}