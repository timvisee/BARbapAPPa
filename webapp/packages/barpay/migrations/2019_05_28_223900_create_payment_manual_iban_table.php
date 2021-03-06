<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentManualIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_manual_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();

            // Target account details, from serviceable
            $table->string('to_account_holder')->nullable(false);
            $table->string('to_iban', 32)->nullable(false);
            $table->string('to_bic', 11)->nullable(true);

            // Source account details, from user
            $table->string('from_iban', 32)->nullable(true);

            // Assessor: last user that checked this payment
            $table->integer('assessor_id')->unsigned()->nullable(true);

            // State
            $table->datetime('transferred_at')->nullable(true);
            $table->datetime('checked_at')->nullable(true);
            $table->datetime('settled_at')->nullable(true);

            $table->timestamps();

            $table->foreign('assessor_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_manual_iban');
    }
}
