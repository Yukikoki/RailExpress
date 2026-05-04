<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Cek dulu apakah kolom seat_id ada sebelum dihapus
            if (Schema::hasColumn('bookings', 'seat_id')) {
                // Hapus foreign key-nya dulu, baru kolomnya
                $table->dropForeign(['seat_id']);
                $table->dropColumn('seat_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('seat_id')->after('schedule_id')->nullable();
            $table->foreign('seat_id')->references('id')->on('seats');
        });
    }
};
