<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutation', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('transaction_id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->morphs('mutationable');
            $table->decimal('amount')->nullable(false);
            $table->integer('currency_id')->unsigned();
            $table->integer('state')->unsigned()->nullable(false)->default(1);
            $table->integer('depend_on')->unsigned()->nullable(true);
            $table->integer('owner_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transaction')
                ->onDelete('cascade');
            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency')
                ->onDelete('restrict');
            $table->foreign('depend_on')
                ->references('id')
                ->on('mutation')
                ->onDelete('set null');
            $table->foreign('owner_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');

            $table->index(['mutationable_id', 'mutationable_type']);
        });

        // Link balance import changes to mutations
        Schema::table('balance_import_change', function (Blueprint $table) {
            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutation')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Linked balance import changes to mutations
        Schema::table('balance_import_change', function (Blueprint $table) {
            $table->dropForeign(['mutation_id']);
        });

        Schema::dropIfExists('mutation');
    }
}
