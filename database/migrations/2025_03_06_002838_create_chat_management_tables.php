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
        Schema::create('contacts', function (Blueprint $table) {
            // المحادثات
            $table->id(); // معرف المحادثة
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم الأول
            $table->foreignId('friend_id')->constrained('users'); // معرف المستخدم الثاني
            $table->enum('friend_type', ['مشرف', 'معلم', 'طالب']); // الحالة
            $table->boolean('user_blocked')->default(false);
            $table->boolean('friend_blocked')->default(false);
            $table->timestamps();
        });
        
        Schema::create('chat_messages', function (Blueprint $table) {
            // رسائل المحادثات
            $table->id(); // معرف الرسالة
            $table->foreignId('contact_id')->constrained('contacts'); // معرف المحادثة
            $table->foreignId('sender_id')->constrained('users'); // معرف المرسل
            $table->foreignId('receiver_id')->constrained('users'); // معرف المرسل
            $table->text('message'); // الرسالة
            $table->boolean('is_read')->default(false); // تم القراءة
            $table->boolean('is_seen')->default(false); // تم القراءة
            $table->boolean('is_deleted')->default(false);
            $table->boolean('has_media')->default(false);
            $table->dateTime('read_date')->default(now()); // التاريخ
            $table->string('file_url')->nullable(); // رابط الملف
            $table->string('file_name')->nullable(); // رابط الملف
            $table->enum('type', ['نص', 'صورة', 'فيديو', 'ملف', 'صوت'])->default('نص');
            $table->bigInteger('size')->default(0);
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            // المحادثات
            $table->id(); // معرف المحادثة
            $table->string('name')->nullable(); // رابط الملف
            $table->string('description')->nullable();
            $table->foreignId('creator_id')->constrained('users');
            $table->boolean('is_private')->default(false);
            $table->string('image_url')->nullable(); // رابط الملف
            $table->timestamps();
        });

        Schema::create('group_members', function (Blueprint $table) {
            // المحادثات
            $table->id(); // معرف المحادثة
            $table->foreignId('group_id')->constrained('groups'); // معرف المحادثة
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم الأول
            $table->boolean('is_admin')->default(false);
            $table->dateTime('joined_date')->default(now());
            $table->enum('role', ['مشرف', 'عضو'])->default('عضو');
            $table->timestamps();
        });
        
        Schema::create('group_messages', function (Blueprint $table) {
            // رسائل المحادثات
            $table->id(); // معرف الرسالة
            $table->foreignId('group_id')->constrained('groups'); // معرف المحادثة
            $table->foreignId('sender_id')->constrained('users'); // معرف المرسل
            $table->text('message'); // الرسالة
            $table->boolean('is_deleted')->default(false);
            $table->boolean('has_media')->default(false);
            $table->string('file_url')->nullable(); // رابط الملف
            $table->enum('type', ['نص', 'صورة', 'فيديو', 'ملف', 'صوت']);
            $table->bigInteger('size')->default(0);
            $table->timestamps();
        });

        Schema::create('read_receipts', function (Blueprint $table) {
            // رسائل المحادثات
            $table->id(); // معرف الرسالة
            $table->foreignId('message_id')->constrained('group_messages'); // معرف المحادثة
            $table->foreignId('user_id')->constrained('users'); // معرف المرسل
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // // القناة
        // Schema::create('channels', function (Blueprint $table) {
        //     $table->id(); // معرف القناة
        //     $table->string('name'); // اسم القناة
        //     $table->text('description')->nullable(); // الوصف
        //     $table->foreignId('college_id')->constrained('colleges'); // معرف المحادثة
        //     $table->foreignId('department_id')->constrained('departments'); // معرف المحادثة
        //     $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
        //     $table->timestamps();
        // });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('group_messages');
        Schema::dropIfExists('read_receipts');
    }
};
