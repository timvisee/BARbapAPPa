<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpdateLaravel8 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Add UUID column
        Schema::table('job_failed', function (Blueprint $table) {
            $table->string('uuid')->after('id')->nullable()->unique();
        });

        // Add UUID for existing entries
        DB::table('job_failed')->whereNull('uuid')->cursor()->each(function ($job) {
            DB::table('job_failed')
                ->where('id', $job->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('job_failed', function(Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
