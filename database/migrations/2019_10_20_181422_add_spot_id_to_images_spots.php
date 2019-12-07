<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpotIdToImagesSpots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images_spots', function (Blueprint $table) {
            $table->unsignedBigInteger('spot_id')->after('user_id');

            $table->foreign('spot_id')
                ->references('id')
                ->on('spots')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images_spots', function (Blueprint $table) {
            //
        });
    }
}
