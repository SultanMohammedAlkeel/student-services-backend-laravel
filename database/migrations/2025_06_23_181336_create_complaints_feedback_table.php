<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // تم التأكد من أنه نفس نوع users.id
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['complaint', 'feedback'])->default('feedback');
            $table->enum('status', ['new', 'read', 'archived'])->default('new');
            $table->timestamps();

            // تعريف المفتاح الخارجي مع التحقق من وجود الجدول أولاً
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            }

            // الفهارس
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints_feedback', function (Blueprint $table) {
            // حذف المفتاح الخارجي أولاً
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('complaints_feedback');
    }
};