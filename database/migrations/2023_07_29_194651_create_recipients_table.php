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
        // المستلمين لرسالة
        Schema::create('recipients', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // المستخدم
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete(); // الرسالة
            $table->timestamp('read_at')->nullable(); // وقت قراءة الرسالة
            $table->softDeletes();
            $table->primary(['user_id','message_id']); // مفتاح مركب للجدول
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipients');
    }
};
