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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("conversation_id")->constrained("conversations")->cascadeOnDelete(); // المحادثة
            $table->foreignId("user_id")->nullable()->constrained("users")->nullOnDelete(); // المرسل
            $table->text("body"); // محتوى الرسالة
            $table->enum('type',['text','attachment'])->default('text'); // نوع الرسالة
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
