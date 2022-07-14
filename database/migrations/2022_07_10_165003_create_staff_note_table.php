<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_note', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff_id');
            $table->date('date_note');
            $table->string('type_note_id');
            $table->text('reason_note');
            $table->tinyInteger('isConfirm')->default(0);
            $table->string('code')->nullable();
            $table->integer('leader_accepted_id')->nullable();
            $table->integer('user_accept_id')->nullable();
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
        Schema::dropIfExists('staff_note');
    }
}
