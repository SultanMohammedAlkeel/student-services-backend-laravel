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
        Schema::create('posts', function (Blueprint $table) {
            // المنشورات
            $table->id(); // معرف المنشور
            $table->foreignId('sender_id')->constrained('users'); // معرف المرسل
            $table->text('content'); // المحتوى
            $table->string('file_url')->nullable(); // رابط الملف
            $table->enum('file_type', ['نص', 'صورة', 'فيديو', 'ملف', 'صوت'])->default('نص');
            $table->bigInteger('file_size')->default(0);
            $table->bigInteger('views_count')->default(0);
            $table->bigInteger('likes_count')->default(0);
            $table->bigInteger('comments_count')->default(0);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
        
        Schema::create('comments', function (Blueprint $table) {
            // التعليقات
            $table->id(); // معرف التعليق
            $table->foreignId('post_id')->constrained('posts'); // معرف المنشور
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم
            $table->text('content'); // المحتوى
            $table->timestamps();
        });

        // التفاعل
        Schema::create('interactions', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('post_id')->constrained('posts'); // معرف المنشور
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم
            $table->boolean('like')->default(false);
            $table->boolean('save')->default(false);
            $table->timestamps();
        });
        
        Schema::create('events', function (Blueprint $table) {
            // الأحداث
            $table->id(); // معرف الحدث
            $table->string('title'); // العنوان
            $table->text('description')->nullable(); // الوصف
            $table->dateTime('date')->nullable(); // التاريخ
            $table->string('location')->nullable(); // الموقع
            $table->foreignId('organizer_id')->constrained('users'); // معرف المنظم
            $table->string('image_url')->nullable(); // رابط الصورة
            $table->timestamps();
        });

        Schema::create('complaints', function (Blueprint $table) {
            // الشكاوى
            $table->id(); // معرف الشكوى
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم
            $table->string('title'); // العنوان
            $table->text('description')->nullable(); // الوصف
            $table->dateTime('date')->default(now()); // التاريخ
            $table->enum('status', ['Pending', 'Resolved', 'Closed'])->default('Pending'); // الحالة
            $table->timestamps();
        });
        
        Schema::create('reports', function (Blueprint $table) {
            // التقارير
            $table->id(); // معرف التقرير
            $table->foreignId('student_id')->constrained('students'); // معرف الطالب
            $table->foreignId('teacher_id')->constrained('teachers'); // معرف المعلم
            $table->string('title'); // العنوان
            $table->text('content'); // المحتوى
            $table->string('file_url')->nullable(); // رابط الملف
            $table->date('submission_date')->nullable(); // تاريخ التقديم
            $table->boolean('is_read')->default(false); // تم القراءة
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('student_id')->constrained('students'); 
            $table->foreignId('teacher_id')->constrained('teachers'); 
            $table->foreignId('course_id')->constrained('courses');
            $table->integer('rating')->checkBetween(1, 5);
            $table->text('comment')->nullable(); 
            $table->dateTime('date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('interactions');
        Schema::dropIfExists('events');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('evaluations');
    }
};
