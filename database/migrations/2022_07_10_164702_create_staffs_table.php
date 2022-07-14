<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff_id')->nullable();
            $table->string('staff', 100)->nullable()->collation('utf8_unicode_ci');
            $table->integer('current_holiday')->nullable();
            $table->integer('left_holiday')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->boolean('is_push')->nullable()->default(0);
            $table->integer('is_send_mail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staffs');
    }
}
