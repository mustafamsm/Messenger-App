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
        //المشاركين في الماحدثة
        Schema::create('participants', function (Blueprint $table) {
           $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();// المحادثة
           $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // المستخدم
           $table->enum('role',['admin','member'])->default('member'); // دور المستخدم في المحادثة
           $table->timestamp('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
};
