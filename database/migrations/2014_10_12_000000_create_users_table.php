<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->nullable();
            $table->string('name');
            $table->integer('annual_leave')->nullable();
            $table->integer('left_annual_leave')->nullable();
            $table->string('email')->unique();
            $table->string('full_name')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->time('password_changed_at')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('avatar', 100)->nullable();
            $table->string('sound')->nullable();
            $table->boolean('gender')->nullable()->default(0); // 0: Male | 1: Female
            $table->date('birthday')->nullable();
            $table->boolean('is_active', 1)->nullable()->default(0);
            $table->string('status', 50)->nullable()->default('normal');
            $table->string('language', 10)->default('en')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
